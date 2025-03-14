import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";

interface LoaderData {
  orderId: string;
  failureReason: string;
  amount: number;
}

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const orderId = url.searchParams.get("orderId");
  const failureReason = url.searchParams.get("error") || "Paiement refusé";
  const amount = parseFloat(url.searchParams.get("amount") || "0");

  if (!orderId) {
    throw new Response("Commande introuvable", { status: 404 });
  }

  return json<LoaderData>({ orderId, failureReason, amount });
};

export default function PaymentRejectedPage() {
  const { orderId, failureReason, amount } = useLoaderData<LoaderData>();

  return (
    <div className="container mx-auto p-6">
      <Card className="max-w-lg mx-auto p-6">
        <div className="text-center space-y-6">
          <div className="text-5xl">❌</div>
          
          <h1 className="text-2xl font-bold text-red-600">
            Paiement Non Accepté
          </h1>

          <div className="space-y-4 text-gray-600">
            <p>
              Le paiement de votre commande <strong>#{orderId}</strong> d'un montant
              de <strong>{amount}€</strong> n'a pas pu aboutir.
            </p>
            <p className="text-sm bg-gray-50 p-2 rounded">
              Raison : {failureReason}
            </p>
          </div>

          <div className="flex justify-center gap-4">
            <Button variant="outline" asChild>
              <a href="/cart">Retour au panier</a>
            </Button>
            <Button asChild>
              <a href="/account/orders">Mes commandes</a>
            </Button>
          </div>
        </div>
      </Card>
    </div>
  );
}
