import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { DocumentsService } from "../documents/documents.service";
import * as Stripe from "stripe";
import Alma from "@alma/client";
import Klarna from "@klarna/client";
import { AmazonPayClient } from "@amazonpay/amazon-pay-api-sdk-nodejs";
import { z } from "zod";

const paymentSchema = z.object({
  amount: z.number().positive(),
  currency: z.string().default("EUR"),
  email: z.string().email(),
  installments: z.number().min(1).max(10),
  returnUrl: z.string().url()
});

@Injectable()
export class PaymentService {
  private stripe: Stripe;
  private alma: Alma;
  private klarna: Klarna;
  private amazonPay: AmazonPayClient;

  constructor(
    private prisma: PrismaService,
    private documents: DocumentsService
  ) {
    this.stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
      apiVersion: "2023-10-16"
    });

    this.alma = new Alma(process.env.ALMA_API_KEY!);
    this.klarna = new Klarna({
      username: process.env.KLARNA_USERNAME!,
      password: process.env.KLARNA_PASSWORD!
    });

    this.amazonPay = new AmazonPayClient({
      region: "eu",
      publicKeyId: process.env.AMAZON_PAY_PUBLIC_KEY_ID!,
      privateKey: process.env.AMAZON_PAY_PRIVATE_KEY!,
      sandbox: process.env.NODE_ENV !== "production"
    });
  }

  async createInstallmentPayment(data: unknown, provider: 'alma' | 'klarna') {
    const validated = paymentSchema.parse(data);

    if (provider === 'alma') {
      const payment = await this.alma.payments.create({
        amount: validated.amount,
        installments: validated.installments,
        email: validated.email,
        return_url: `${process.env.FRONTEND_URL}/payment/success`,
        webhook_url: `${process.env.API_URL}/webhooks/alma`
      });

      return { redirectUrl: payment.url };
    }

    if (provider === 'klarna') {
      const order = await this.klarna.orders.create({
        amount: validated.amount * 100,
        installments: validated.installments,
        email: validated.email,
        success_url: `${process.env.FRONTEND_URL}/payment/success`,
        webhook_url: `${process.env.API_URL}/webhooks/klarna`
      });

      return { redirectUrl: order.redirect_url };
    }
  }

  async createSubscription(customerId: string, priceId: string) {
    const subscription = await this.stripe.subscriptions.create({
      customer: customerId,
      items: [{ price: priceId }],
      payment_behavior: 'default_incomplete',
      expand: ['latest_invoice.payment_intent']
    });

    await this.prisma.subscription.create({
      data: {
        stripeId: subscription.id,
        customerId,
        status: subscription.status,
        priceId
      }
    });

    return subscription;
  }

  async createApplePaySession(data: unknown) {
    const validated = paymentSchema.parse(data);

    const paymentIntent = await this.stripe.paymentIntents.create({
      amount: Math.round(validated.amount * 100),
      currency: validated.currency,
      payment_method_types: ["card", "apple_pay"],
      receipt_email: validated.email,
      return_url: validated.returnUrl
    });

    await this.prisma.payment.create({
      data: {
        stripeId: paymentIntent.id,
        amount: validated.amount,
        email: validated.email,
        status: "pending"
      }
    });

    return {
      clientSecret: paymentIntent.client_secret
    };
  }

  async createAmazonPaySession(data: unknown) {
    const validated = paymentSchema.parse(data);

    const payload = {
      webCheckoutDetails: {
        checkoutResultReturnUrl: validated.returnUrl
      },
      paymentDetails: {
        paymentIntent: "Authorize",
        chargeAmount: {
          amount: validated.amount.toString(),
          currencyCode: validated.currency
        }
      }
    };

    const response = await this.amazonPay.createCheckoutSession(payload);

    if (response.status === 201) {
      await this.prisma.payment.create({
        data: {
          amazonPayId: response.checkoutSessionId,
          amount: validated.amount,
          email: validated.email,
          status: "pending"
        }
      });

      return {
        amazonCheckoutSessionId: response.checkoutSessionId
      };
    }

    throw new Error("Erreur lors de la création de la session Amazon Pay");
  }

  async retryFailedPayments() {
    const failedPayments = await this.prisma.payment.findMany({
      where: { 
        status: "failed",
        retryCount: { lt: 3 }
      }
    });

    for (const payment of failedPayments) {
      try {
        if (payment.stripeId) {
          await this.stripe.paymentIntents.confirm(payment.stripeId);
        } else if (payment.amazonPayId) {
          await this.amazonPay.chargePermission({
            chargePermissionId: payment.amazonPayId,
            chargeAmount: {
              amount: payment.amount.toString(),
              currencyCode: "EUR"
            }
          });
        }

        await this.prisma.payment.update({
          where: { id: payment.id },
          data: { status: "processing" }
        });
      } catch (error) {
        await this.prisma.payment.update({
          where: { id: payment.id },
          data: { 
            retryCount: { increment: 1 },
            lastError: error.message
          }
        });
      }
    }
  }

  async handleWebhook(provider: string, event: any) {
    switch (provider) {
      case 'stripe':
        return this.handleStripeWebhook(event);
      case 'alma':
        return this.handleAlmaWebhook(event);
      case 'klarna':
        return this.handleKlarnaWebhook(event);
    }
  }

  private async handleStripeWebhook(event: Stripe.Event) {
    if (event.type === 'payment_intent.succeeded') {
      const payment = event.data.object as Stripe.PaymentIntent;
      await this.documents.generateAndSendInvoice(payment);
    }
  }

  private async handleAlmaWebhook(event: any) {
    if (event.type === 'payment_succeeded') {
      await this.documents.generateAndSendInvoice({
        id: event.data.payment_id,
        amount: event.data.amount,
        customer_email: event.data.customer.email
      });
    }
  }

  private async handleKlarnaWebhook(event: any) {
    if (event.event_type === 'order.completed') {
      await this.documents.generateAndSendInvoice({
        id: event.order_id,
        amount: event.order_amount,
        customer_email: event.customer.email
      });
    }
  }
}
