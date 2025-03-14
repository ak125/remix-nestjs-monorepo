import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Alert } from "@/components/ui/alert";

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const orderId = url.searchParams.get("vads_order_id");
  const result = url.searchParams.get("vads_result");
  
  if (!orderId || !result) {
    throw new Response("Paramètres manquants", { status: 400 });
  }

  return json({
    orderId: orderId.replace("-", "/"),
    isSuccess: result === "00",
  });
};

export default function PaymentResultPage() {
  const { orderId, isSuccess } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-6">
      <Card className="max-w-lg mx-auto p-6">
        <div className="space-y-6 text-center">
          <div className="text-5xl">
            {isSuccess ? "✅" : "❌"}
          </div>

          <h1 className={`text-2xl font-bold ${
            isSuccess ? "text-green-600" : "text-red-600"
          }`}>
            {isSuccess ? "Paiement Accepté" : "Paiement Refusé"}
          </h1>

          <p className="text-gray-600">
            Commande #{orderId}
          </p>

          <Alert variant={isSuccess ? "default" : "destructive"}>
            {isSuccess 
              ? "Votre commande a été validée avec succès."
              : "Le paiement n'a pas pu être validé."
            }
          </Alert>

          <div className="pt-4 flex justify-center gap-4">
            <Button variant="outline" asChild>
              <a href="/">Retour à l'accueil</a>
            </Button>
            <Button asChild>
              <a href="/account/orders">Voir mes commandes</a>
            </Button>
          </div>
        </div>
      </Card>
    </div>
  );
}
