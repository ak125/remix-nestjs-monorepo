import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { NotificationsService } from "../notifications/notifications.service";
import * as Stripe from "stripe";
import { z } from "zod";

const refundSchema = z.object({
  paymentId: z.string(),
  reason: z.enum(['requested_by_customer', 'duplicate', 'fraudulent']),
  amount: z.number().optional(),
  notifyCustomer: z.boolean().default(true)
});

@Injectable()
export class RefundService {
  private stripe: Stripe;

  constructor(
    private prisma: PrismaService,
    private notifications: NotificationsService
  ) {
    this.stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
      apiVersion: "2023-10-16"
    });
  }

  async processRefund(data: unknown) {
    const validated = refundSchema.parse(data);

    const payment = await this.stripe.paymentIntents.retrieve(validated.paymentId);
    
    const refund = await this.stripe.refunds.create({
      payment_intent: validated.paymentId,
      amount: validated.amount,
      reason: validated.reason
    });

    await this.prisma.refund.create({
      data: {
        stripeId: refund.id,
        paymentId: validated.paymentId,
        amount: refund.amount / 100,
        reason: validated.reason,
        status: refund.status
      }
    });

    if (validated.notifyCustomer && payment.receipt_email) {
      await this.notifications.sendPaymentReminder({
        phoneNumber: payment.shipping?.phone || "",
        amount: refund.amount / 100,
        type: 'refund_processed'
      });
    }

    return refund;
  }

  async getRefundHistory(customerId: string) {
    return this.prisma.refund.findMany({
      where: { 
        payment: { customerId } 
      },
      orderBy: { createdAt: 'desc' },
      include: {
        payment: true
      }
    });
  }
}
