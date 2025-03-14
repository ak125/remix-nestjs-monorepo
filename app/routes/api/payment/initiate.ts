import { json, ActionFunction } from "@remix-run/node";
import { PaymentService } from "~/services/payment.server";
import { requireUser } from "~/utils/auth.server";
import { z } from "zod";

const InitiateSchema = z.object({
  orderId: z.string(),
  provider: z.enum(["PAYPAL", "SYSTEMPAY"]),
});

export const action: ActionFunction = async ({ request }) => {
  await requireUser(request);

  try {
    const formData = await request.formData();
    const result = InitiateSchema.safeParse(Object.fromEntries(formData));

    if (!result.success) {
      return json({ error: "Données invalides" }, { status: 400 });
    }

    const paymentService = new PaymentService();
    const { payment, paymentUrl } = await paymentService.initiatePayment(
      result.data.orderId,
      result.data.provider
    );

    return json({ success: true, payment, paymentUrl });

  } catch (error) {
    console.error("Erreur initiation paiement:", error);
    return json(
      { error: "Erreur lors de l'initiation du paiement" },
      { status: 500 }
    );
  }
};
