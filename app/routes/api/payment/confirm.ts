import { json, ActionFunction } from "@remix-run/node";
import { prisma } from "~/utils/db.server";
import { sendNotification } from "~/utils/notifications.server";
import { sendEmail } from "~/utils/mailer.server";
import { z } from "zod";

const PaymentConfirmSchema = z.object({
  orderId: z.string(),
  status: z.enum(["SUCCESS", "FAILED"]),
  amount: z.number().positive(),
  currency: z.string().default("EUR"),
  provider: z.string(),
  providerOrderId: z.string().optional(),
  metadata: z.record(z.unknown()).optional(),
});

export const action: ActionFunction = async ({ request }) => {
  try {
    const formData = await request.formData();
    const result = PaymentConfirmSchema.safeParse(Object.fromEntries(formData));

    if (!result.success) {
      return json({ error: "Données invalides" }, { status: 400 });
    }

    const { orderId, status, amount, currency, provider, providerOrderId, metadata } = result.data;

    // Transaction atomique
    const [payment, order] = await prisma.$transaction([
      prisma.payment.create({
        data: {
          orderId,
          status: status === "SUCCESS" ? "COMPLETED" : "FAILED",
          amount,
          currency,
          provider,
          providerOrderId,
          metadata: metadata || {},
          confirmedAt: status === "SUCCESS" ? new Date() : null,
        },
        include: { order: { include: { customer: true } } },
      }),
      prisma.order.update({
        where: { id: orderId },
        data: {
          status: status === "SUCCESS" ? "PAID" : "PAYMENT_FAILED",
        },
      }),
    ]);

    // Notifications
    if (status === "SUCCESS") {
      await Promise.all([
        sendNotification({
          type: "PAYMENT_CONFIRMED",
          title: "Paiement confirmé",
          message: `Commande ${orderId} payée (${amount}${currency})`,
          metadata: { orderId, amount, currency },
        }),
        sendEmail({
          to: payment.order.customer.email,
          template: "payment-confirmation",
          data: {
            orderId,
            amount,
            currency,
            customerName: payment.order.customer.name,
          },
        }),
      ]);
    }

    return json({ success: true, payment });

  } catch (error) {
    console.error("Erreur confirmation paiement:", error);
    return json(
      { error: "Erreur lors de la confirmation" }, 
      { status: 500 }
    );
  }
};
