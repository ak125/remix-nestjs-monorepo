import { json, ActionFunction } from "@remix-run/node";
import { prisma } from "~/utils/db.server";
import { emitAdminNotification } from "~/utils/notifications.server";

export const action: ActionFunction = async ({ request }) => {
  try {
    const formData = await request.formData();
    const commandeId = formData.get("commande_id")?.toString().replace("-", "/");
    const erreur = formData.get("error")?.toString();

    if (!commandeId) {
      throw new Error("Identifiant de commande manquant");
    }

    // Mise à jour de la commande et création de la notification
    const [order] = await prisma.$transaction([
      prisma.order.update({
        where: { id: commandeId },
        data: { 
          status: "CANCELLED",
          cancelReason: erreur || "Annulation par l'utilisateur",
          cancelledAt: new Date(),
        },
        include: { customer: true },
      }),
      prisma.adminNotification.create({
        data: {
          type: "PAYMENT_CANCELLED",
          title: "Paiement annulé",
          message: `Commande ${commandeId} annulée`,
          metadata: {
            orderId: commandeId,
            reason: erreur,
          },
        },
      }),
    ]);

    // Émettre la notification admin
    await emitAdminNotification({
      type: "PAYMENT_CANCELLED",
      orderId: commandeId,
      customerId: order.customer.id,
    });

    return json({ success: true });

  } catch (error) {
    console.error("Erreur annulation paiement:", error);
    return json({ error: "Erreur lors de l'annulation" }, { status: 500 });
  }
};
