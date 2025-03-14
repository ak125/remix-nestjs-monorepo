import { json, ActionFunction } from "@remix-run/node";
import { RejectedPaymentsService } from "~/services/rejected-payments.server";
import { z } from "zod";

const RejectionSchema = z.object({
  orderId: z.string(),
  reason: z.string(),
  errorCode: z.string().optional(),
  metadata: z.record(z.unknown()).optional(),
});

export const action: ActionFunction = async ({ request }) => {
  try {
    const formData = await request.formData();
    const result = RejectionSchema.safeParse(Object.fromEntries(formData));

    if (!result.success) {
      return json({ error: "Données invalides" }, { status: 400 });
    }

    const service = new RejectedPaymentsService();
    const { payment, rejection } = await service.handleRejection(result.data);

    return json({ 
      success: true, 
      remainingRetries: payment.maxRetries - payment.retryCount,
      canRetry: payment.retryCount < payment.maxRetries,
    });

  } catch (error) {
    console.error("Erreur rejet paiement:", error);
    return json(
      { error: "Erreur lors du traitement du rejet" },
      { status: 500 }
    );
  }
};
