import { prisma } from "~/utils/db.server";
import { createAdminNotification } from "~/utils/notifications.server";
import crypto from 'crypto';
import Stripe from 'stripe';

const stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
  apiVersion: '2023-10-16'
});

interface CreateOrderParams {
  userId: string;
  paymentMethod: string;
  items: Array<{
    productId: string;
    productName: string;
    quantity: number;
    unitPrice: number;
  }>;
}

interface ProcessPaymentParams {
  orderId: string;
  provider: string;
  amount: number;
  metadata?: Record<string, any>;
}

interface PaymentConfig {
  PAYPAL: {
    baseUrl: string;
    clientId: string;
    secret: string;
  };
  SYSTEMPAY: {
    baseUrl: string;
    merchantId: string;
    certificate: string;
  };
}

const PAYMENT_CONFIG: PaymentConfig = {
  PAYPAL: {
    baseUrl: process.env.PAYPAL_API_URL || "https://api-m.sandbox.paypal.com",
    clientId: process.env.PAYPAL_CLIENT_ID!,
    secret: process.env.PAYPAL_SECRET!,
  },
  SYSTEMPAY: {
    baseUrl: "https://paiement.systempay.fr/vads-payment",
    merchantId: process.env.SYSTEMPAY_MERCHANT_ID!,
    certificate: process.env.SYSTEMPAY_CERTIFICATE!,
  },
};

interface PaymentParams {
  amount: number;
  orderId: string;
  currency?: string;
  locale?: string;
}

export class PaymentService {
  private readonly merchantId = process.env.SYSTEMPAY_MERCHANT_ID!;
  private readonly secretKey = process.env.SYSTEMPAY_SECRET_KEY!;
  private readonly testMode = process.env.NODE_ENV !== 'production';

  async createOrder({ userId, paymentMethod, items }: CreateOrderParams) {
    const totalAmount = items.reduce(
      (sum, item) => sum + item.quantity * item.unitPrice,
      0
    );

    return await prisma.order.create({
      data: {
        userId,
        paymentMethod,
        totalAmount,
        items: {
          create: items.map(item => ({
            productId: item.productId,
            productName: item.productName,
            quantity: item.quantity,
            unitPrice: item.unitPrice,
            totalPrice: item.quantity * item.unitPrice,
          })),
        },
      },
      include: {
        items: true,
        customer: true,
      },
    });
  }

  async processPayment({ orderId, provider, amount, metadata }: ProcessPaymentParams) {
    const [payment, order] = await prisma.$transaction([
      prisma.payment.create({
        data: {
          orderId,
          provider,
          amount,
          metadata,
          status: "SUCCESS",
          completedAt: new Date(),
        },
      }),
      prisma.order.update({
        where: { id: orderId },
        data: { 
          status: "PAID",
          updatedAt: new Date(),
        },
      }),
    ]);

    return { payment, order };
  }

  async refundPayment(orderId: string, reason: string) {
    const [payment, order] = await prisma.$transaction([
      prisma.payment.update({
        where: { orderId },
        data: {
          status: "REFUNDED",
          refundedAt: new Date(),
          metadata: {
            refundReason: reason,
          },
        },
      }),
      prisma.order.update({
        where: { id: orderId },
        data: {
          status: "REFUNDED",
          updatedAt: new Date(),
        },
      }),
    ]);

    return { payment, order };
  }

  async initiatePayment(orderId: string, provider: string) {
    const order = await prisma.order.findUnique({
      where: { id: orderId },
      include: { customer: true },
    });

    if (!order) {
      throw new Error("Order not found");
    }

    const payment = await prisma.payment.create({
      data: {
        orderId,
        provider,
        amount: order.totalAmount,
        status: "PENDING",
      },
    });

    const paymentUrl = await this.generatePaymentUrl(payment, order);

    await createAdminNotification({
      type: "PAYMENT_INITIATED",
      title: `Nouveau paiement ${provider}`,
      message: `Commande ${orderId} - ${order.totalAmount}€`,
      category: "PAYMENT",
      metadata: { orderId, provider },
    });

    return { payment, paymentUrl };
  }

  private async generatePaymentUrl(payment: any, order: any) {
    switch (payment.provider) {
      case "PAYPAL":
        return this.generatePayPalUrl(payment, order);
      case "SYSTEMPAY":
        return this.generateSystemPayUrl(payment, order);
      default:
        throw new Error(`Provider ${payment.provider} not supported`);
    }
  }

  private async generatePayPalUrl(payment: any, order: any) {
    // Implementation PayPal...
  }

  private generateSystemPayUrl(payment: any, order: any) {
    const { merchantId, certificate } = PAYMENT_CONFIG.SYSTEMPAY;
    const amount = Math.round(payment.amount * 100);
    const dateTime = new Date().toISOString().replace(/[-:]/g, "");

    const signature = this.generateSystemPaySignature({
      amount,
      orderId: payment.orderId,
      dateTime,
      merchantId,
      certificate,
    });

    return {
      url: PAYMENT_CONFIG.SYSTEMPAY.baseUrl,
      params: {
        vads_amount: amount,
        vads_ctx_mode: "PRODUCTION",
        vads_currency: "978",
        vads_order_id: payment.orderId,
        vads_payment_config: "SINGLE",
        vads_site_id: merchantId,
        vads_trans_date: dateTime,
        signature,
      },
    };
  }

  private generateSystemPaySignature(params: any) {
    // Implementation signature SystemPay...
  }

  generatePaymentUrl(params: PaymentParams) {
    const systemPayParams = {
      vads_action_mode: "INTERACTIVE",
      vads_amount: Math.round(params.amount * 100),
      vads_currency: params.currency || "978",
      vads_ctx_mode: this.testMode ? "TEST" : "PRODUCTION",
      vads_page_action: "PAYMENT",
      vads_payment_config: "SINGLE",
      vads_site_id: this.merchantId,
      vads_trans_date: new Date().toISOString().replace(/[-:T]/g, "").slice(0, 14),
      vads_trans_id: params.orderId.slice(0, 6),
      vads_version: "V2",
      vads_language: params.locale || "fr",
    };

    const signature = this.generateSignature(systemPayParams);

    return `https://secure.systempay.fr/vads-payment/?${new URLSearchParams({
      ...systemPayParams,
      signature
    })}`;
  }

  private generateSignature(params: Record<string, string | number>) {
    const sortedKeys = Object.keys(params).sort();
    const signData = sortedKeys.map(key => params[key]).join("+") + "+" + this.secretKey;
    return crypto.createHmac("sha256", this.secretKey).update(signData).digest("hex");
  }

  async createCheckoutSession(userId: string, cartId: string) {
    const cart = await prisma.cart.findUnique({
      where: { id: cartId },
      include: {
        items: true,
        user: true
      }
    });

    if (!cart) throw new Error('Cart not found');

    const session = await stripe.checkout.sessions.create({
      customer_email: cart.user.email,
      line_items: cart.items.map(item => ({
        price_data: {
          currency: 'eur',
          product_data: {
            name: item.productId // You should fetch product details here
          },
          unit_amount: Math.round(item.price * 100)
        },
        quantity: item.quantity
      })),
      mode: 'payment',
      success_url: `${process.env.PUBLIC_URL}/order/success?session_id={CHECKOUT_SESSION_ID}`,
      cancel_url: `${process.env.PUBLIC_URL}/cart`
    });

    await prisma.order.create({
      data: {
        userId,
        items: cart.items,
        total: cart.items.reduce((sum, item) => sum + item.price * item.quantity, 0),
        stripeId: session.id
      }
    });

    return session;
  }

  async handleWebhook(signature: string, payload: Buffer) {
    const event = stripe.webhooks.constructEvent(
      payload,
      signature,
      process.env.STRIPE_WEBHOOK_SECRET!
    );

    if (event.type === 'checkout.session.completed') {
      const session = event.data.object as Stripe.Checkout.Session;
      
      await prisma.order.update({
        where: { stripeId: session.id },
        data: { status: 'PAID' }
      });
    }
  }
}
