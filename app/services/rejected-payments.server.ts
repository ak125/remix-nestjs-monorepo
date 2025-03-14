import { prisma } from "~/utils/db.server";
import { sendEmail } from "~/utils/mailer.server";
import { createAdminNotification } from "~/utils/notifications.server";

interface HandleRejectionParams {
  orderId: string;
  reason: string;
  errorCode?: string;
  metadata?: Record<string, any>;
}

export class RejectedPaymentsService {
  async handleRejection({
    orderId,
    reason,
    errorCode,
    metadata,
  }: HandleRejectionParams) {
    const payment = await prisma.payment.findUnique({
      where: { orderId },
      include: {
        order: {
          include: { customer: true },
        },
      },
    });

    if (!payment) {
      throw new Error("Payment not found");
    }

    // Transaction atomique
    const [updatedPayment, rejection] = await prisma.$transaction([
      // Mise à jour du paiement
      prisma.payment.update({
        where: { id: payment.id },
        data: {
          status: "FAILED",
          lastFailureAt: new Date(),
          retryCount: { increment: 1 },
        },
      }),
      // Création de l'historique
      prisma.rejectedPayment.create({
        data: {
          paymentId: payment.id,
          reason,
          errorCode,
          metadata,
        },
      }),
    ]);

    // Notifications
    await Promise.all([
      // Email client
      sendEmail({
        to: payment.order.customer.email,
        template: "payment-rejected",
        data: {
          orderId,
          reason,
          amount: payment.amount,
          retryCount: updatedPayment.retryCount,
          maxRetries: payment.maxRetries,
        },
      }),
      // Notification admin
      createAdminNotification({
        type: "PAYMENT_REJECTED",
        title: `Paiement rejeté - Commande ${orderId}`,
        message: reason,
        priority: "HIGH",
        metadata: {
          orderId,
          errorCode,
          retryCount: updatedPayment.retryCount,
        },
      }),
    ]);

    return { payment: updatedPayment, rejection };
  }
}
