import { json, ActionFunction } from "@remix-run/node";
import { PaymentAbortService } from "~/services/payment-abort.server";
import { z } from "zod";

const AbortSchema = z.object({
  orderId: z.string(),
  reason: z.string(),
  code: z.string().optional(),
  details: z.record(z.unknown()).optional(),
});

export const action: ActionFunction = async ({ request }) => {
  try {
    const formData = await request.formData();
    const result = AbortSchema.safeParse(Object.fromEntries(formData));

    if (!result.success) {
      return json({ error: "Données invalides" }, { status: 400 });
    }

    const service = new PaymentAbortService();
    const { payment, abortRecord } = await service.handleAbort(result.data);

    return json({
      success: true,
      payment,
      abortRecord,
    });

  } catch (error) {
    console.error("Erreur annulation paiement:", error);
    return json(
      { error: "Erreur lors de l'annulation" },
      { status: 500 }
    );
  }
};
