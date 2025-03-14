import { prisma } from "~/utils/db.server";
import { sendNotification } from "~/utils/notifications.server";
import { sendEmail } from "~/utils/mailer.server";

interface HandlePaymentFailureParams {
  orderId: string;
  failureCode: string;
  failureReason: string;
  metadata?: Record<string, any>;
}

export class PaymentFailureService {
  async handleFailure({
    orderId,
    failureCode,
    failureReason,
    metadata,
  }: HandlePaymentFailureParams) {
    const [order, payment] = await prisma.$transaction([
      prisma.order.update({
        where: { id: orderId },
        data: { 
          status: "PAYMENT_FAILED",
          updatedAt: new Date(),
        },
        include: { customer: true },
      }),
      prisma.payment.update({
        where: { orderId },
        data: {
          status: "FAILED",
          failureCode,
          failureReason,
          failedAt: new Date(),
          lastAttemptAt: new Date(),
          attempts: { increment: 1 },
          metadata: metadata || {},
        },
      }),
    ]);

    // Notifications
    await Promise.all([
      // Admin notification
      sendNotification({
        type: "PAYMENT_FAILED",
        title: "Échec de paiement",
        message: `Commande ${orderId} : ${failureReason}`,
        metadata: { orderId, failureCode },
        priority: "HIGH",
      }),
      // Customer email
      sendEmail({
        to: order.customer.email,
        template: "payment-failed",
        data: {
          customerName: order.customer.name,
          orderId,
          amount: order.totalAmount,
          failureReason,
        },
      }),
    ]);

    return { order, payment };
  }
}
