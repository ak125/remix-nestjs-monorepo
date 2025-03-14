import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import * as Stripe from "stripe";
import Alma from "@alma/client";
import { z } from "zod";

const paymentMethodSchema = z.object({
  amount: z.number().positive(),
  currency: z.string().default("EUR"),
  email: z.string().email(),
  installments: z.number().min(1).max(10).optional(),
  returnUrl: z.string().url()
});

@Injectable()
export class AlternativePaymentService {
  private stripe: Stripe;
  private alma: Alma;

  constructor(private prisma: PrismaService) {
    this.stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
      apiVersion: "2023-10-16"
    });

    this.alma = new Alma(process.env.ALMA_API_KEY!);
  }

  async createKlarnaPayment(data: unknown) {
    const validated = paymentMethodSchema.parse(data);

    const paymentIntent = await this.stripe.paymentIntents.create({
      amount: Math.round(validated.amount * 100),
      currency: validated.currency,
      payment_method_types: ["klarna"],
      receipt_email: validated.email,
      return_url: validated.returnUrl
    });

    await this.prisma.payment.create({
      data: {
        stripeId: paymentIntent.id,
        amount: validated.amount,
        email: validated.email,
        method: "klarna",
        status: "pending"
      }
    });

    return { clientSecret: paymentIntent.client_secret };
  }

  async createAlmaPayment(data: unknown) {
    const validated = paymentMethodSchema.parse(data);

    const payment = await this.alma.payments.create({
      amount: validated.amount,
      installments_count: validated.installments || 3,
      return_url: validated.returnUrl,
      customer: {
        email: validated.email
      }
    });

    await this.prisma.payment.create({
      data: {
        almaId: payment.id,
        amount: validated.amount,
        email: validated.email,
        method: "alma",
        status: "pending"
      }
    });

    return { checkoutUrl: payment.url };
  }

  async createPayPalPayment(data: unknown) {
    const validated = paymentMethodSchema.parse(data);

    const paymentIntent = await this.stripe.paymentIntents.create({
      amount: Math.round(validated.amount * 100),
      currency: validated.currency,
      payment_method_types: ["paypal"],
      receipt_email: validated.email,
      return_url: validated.returnUrl
    });

    await this.prisma.payment.create({
      data: {
        stripeId: paymentIntent.id,
        amount: validated.amount,
        email: validated.email,
        method: "paypal",
        status: "pending"
      }
    });

    return { clientSecret: paymentIntent.client_secret };
  }
}
