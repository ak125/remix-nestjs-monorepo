import { prisma } from "~/utils/db.server";
import { sendEmail } from "~/utils/mailer.server";
import { createAdminNotification } from "~/utils/notifications.server";

interface HandleAbortParams {
  orderId: string;
  reason: string;
  code?: string;
  details?: Record<string, any>;
}

export class PaymentAbortService {
  async handleAbort({ orderId, reason, code, details }: HandleAbortParams) {
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
    const [updatedPayment, abortRecord] = await prisma.$transaction([
      prisma.payment.update({
        where: { id: payment.id },
        data: {
          status: "ABORTED",
          abortedAt: new Date(),
          abortReason: reason,
          abortCode: code,
        },
      }),
      prisma.paymentAborted.create({
        data: {
          paymentId: payment.id,
          reason,
          code,
          details: details || {},
        },
      }),
    ]);

    // Notifications
    await Promise.all([
      sendEmail({
        to: payment.order.customer.email,
        template: "payment-aborted",
        data: {
          orderId,
          reason,
          amount: payment.amount,
        },
      }),
      createAdminNotification({
        type: "PAYMENT_ABORTED",
        title: `Paiement annulé - Commande ${orderId}`,
        message: reason,
        category: "PAYMENT",
        priority: "HIGH",
        metadata: { orderId, code },
      }),
    ]);

    return { payment: updatedPayment, abortRecord };
  }
}
