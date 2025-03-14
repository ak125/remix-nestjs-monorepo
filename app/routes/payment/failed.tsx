import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Alert, AlertDescription } from "@/components/ui/alert";

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const orderId = url.searchParams.get("commande_id");
  const error = url.searchParams.get("error");

  if (!orderId) {
    throw new Response("Commande introuvable", { status: 404 });
  }

  // Log l'erreur côté serveur
  await fetch(`${process.env.API_URL}/payments/failed`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ commande_id: orderId, error }),
  });

  return json({ orderId, error });
};

export default function PaymentFailedPage() {
  const { orderId, error } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-6">
      <Card className="max-w-lg mx-auto p-6">
        <div className="space-y-6">
          <div className="text-center">
            <h1 className="text-2xl font-bold text-red-600">
              Paiement Non Validé
            </h1>
            <p className="text-gray-500 mt-2">
              Commande #{orderId}
            </p>
          </div>

          <Alert variant="destructive">
            <AlertDescription>
              {error || "Une erreur est survenue lors du paiement"}
            </AlertDescription>
          </Alert>

          <div className="prose max-w-none">
            <p>
              Votre commande a été enregistrée mais le paiement n'a pas pu être validé.
              Vous pouvez :
            </p>
            <ul>
              <li>Réessayer le paiement depuis votre espace client</li>
              <li>Contacter notre service client</li>
              <li>Choisir un autre moyen de paiement</li>
            </ul>
          </div>

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
