import { json, ActionFunction } from "@remix-run/node";
import { PaymentService } from "~/services/payment.server";
import { requireUser } from "~/utils/auth.server";
import { z } from "zod";

const PaymentSchema = z.object({
  orderId: z.string(),
  provider: z.enum(["PAYPAL", "STRIPE"]),
  amount: z.number().positive(),
  metadata: z.record(z.unknown()).optional(),
});

export const action: ActionFunction = async ({ request }) => {
  const user = await requireUser(request);
  const paymentService = new PaymentService();

  try {
    const formData = await request.formData();
    const result = PaymentSchema.safeParse(Object.fromEntries(formData));

    if (!result.success) {
      return json({ error: "Données invalides" }, { status: 400 });
    }

    const { payment, order } = await paymentService.processPayment({
      ...result.data,
      metadata: {
        ...result.data.metadata,
        userId: user.id,
      },
    });

    return json({ success: true, payment, order });

  } catch (error) {
    console.error("Erreur traitement paiement:", error);
    return json(
      { error: "Erreur lors du traitement du paiement" },
      { status: 500 }
    );
  }
};
