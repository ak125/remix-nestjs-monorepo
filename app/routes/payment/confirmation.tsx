import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { getSession } from "@/utils/session.server";

export const loader: LoaderFunction = async ({ request }) => {
  const session = await getSession(request.headers.get("Cookie"));
  const url = new URL(request.url);
  const orderId = url.searchParams.get("orderId");

  if (!session.has("userId") || !orderId) {
    throw new Response("Non autorisé", { status: 401 });
  }

  const order = await fetch(`${process.env.API_URL}/orders/${orderId}`, {
    headers: { Cookie: request.headers.get("Cookie") || "" },
  }).then(r => r.json());

  return json({ order });
};

export default function PaymentConfirmationPage() {
  const { order } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-6">
      <Card className="max-w-lg mx-auto p-6">
        <div className="text-center space-y-6">
          <div className="text-5xl">✅</div>
          
          <div>
            <h1 className="text-2xl font-bold text-green-600">
              Paiement Confirmé
            </h1>
            <p className="text-gray-500 mt-2">
              Commande #{order.id}
            </p>
          </div>

          <p className="text-gray-600">
            Merci pour votre commande. Un email de confirmation 
            vous a été envoyé.
          </p>

          <div className="flex justify-center gap-4">
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
