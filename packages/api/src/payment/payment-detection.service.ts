import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { NotificationsService } from "../notifications/notifications.service";
import * as Stripe from "stripe";
import { z } from "zod";

const paymentSchema = z.object({
  userId: z.string(),
  customerId: z.string(),
  amount: z.number(),
  currency: z.string(),
  created: z.number()
});

@Injectable()
export class PaymentDetectionService {
  private stripe: Stripe;

  constructor(
    private prisma: PrismaService,
    private notifications: NotificationsService
  ) {
    this.stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
      apiVersion: "2023-10-16"
    });
  }

  async checkForDuplicatePayments(customerId: string) {
    // Récupérer les paiements récents
    const payments = await this.stripe.paymentIntents.list({
      customer: customerId,
      limit: 10
    });

    const duplicates = this.findDuplicates(payments.data);
    
    for (const duplicate of duplicates) {
      await this.handleDuplicatePayment(duplicate);
    }

    return duplicates;
  }

  private findDuplicates(payments: Stripe.PaymentIntent[]): Stripe.PaymentIntent[] {
    const duplicates: Stripe.PaymentIntent[] = [];
    
    for (let i = 0; i < payments.length - 1; i++) {
      const current = payments[i];
      const next = payments[i + 1];

      if (this.isDuplicate(current, next)) {
        duplicates.push(current);
      }
    }

    return duplicates;
  }

  private isDuplicate(payment1: Stripe.PaymentIntent, payment2: Stripe.PaymentIntent): boolean {
    return (
      payment1.amount === payment2.amount &&
      payment1.currency === payment2.currency &&
      Math.abs(payment1.created - payment2.created) < 300 // 5 minutes
    );
  }

  private async handleDuplicatePayment(payment: Stripe.PaymentIntent) {
    // Créer le remboursement
    const refund = await this.stripe.refunds.create({
      payment_intent: payment.id,
      reason: 'duplicate'
    });

    // Enregistrer dans la base de données
    await this.prisma.refund.create({
      data: {
        stripeId: refund.id,
        paymentId: payment.id,
        amount: payment.amount / 100,
        reason: 'duplicate',
        status: refund.status
      }
    });

    // Notifier le client
    if (payment.receipt_email) {
      await this.notifications.sendPaymentReminder({
        type: 'refund_processed',
        amount: payment.amount / 100,
        email: payment.receipt_email
      });
    }

    return refund;
  }

  async getFailedPaymentsStats(startDate: string, endDate: string) {
    const [dailyStats, totalStats] = await Promise.all([
      this.prisma.payment.groupBy({
        by: ['createdAt'],
        where: {
          status: 'failed',
          createdAt: {
            gte: new Date(startDate),
            lte: new Date(endDate)
          }
        },
        _count: true,
        _sum: {
          amount: true
        }
      }),
      this.prisma.payment.aggregate({
        where: {
          status: 'failed',
          createdAt: {
            gte: new Date(startDate),
            lte: new Date(endDate)
          }
        },
        _count: true,
        _sum: {
          amount: true
        }
      })
    ]);

    return {
      daily: dailyStats.map(stat => ({
        date: stat.createdAt,
        count: stat._count,
        amount: stat._sum.amount
      })),
      total: {
        count: totalStats._count,
        amount: totalStats._sum.amount
      }
    };
  }
}
