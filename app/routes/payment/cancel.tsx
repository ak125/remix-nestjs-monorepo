import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Alert } from "@/components/ui/alert";

interface LoaderData {
  commandeId: string;
  erreur: string;
}

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const commandeId = url.searchParams.get("commande_id")?.replace("-", "/");
  const erreur = url.searchParams.get("error") || "Annulation demandée";

  if (!commandeId) {
    throw new Response("Commande introuvable", { status: 404 });
  }

  return json<LoaderData>({ commandeId, erreur });
};

export default function PaymentCancelPage() {
  const { commandeId, erreur } = useLoaderData<LoaderData>();

  return (
    <div className="container mx-auto p-6">
      <Card className="max-w-lg mx-auto p-6">
        <div className="text-center space-y-6">
          <div className="text-5xl">❌</div>

          <h1 className="text-2xl font-bold text-red-600">
            Paiement Annulé
          </h1>

          <Alert variant="destructive">
            Commande #{commandeId} non validée
          </Alert>

          <div className="space-y-4 text-gray-600">
            <p>
              Votre commande a été enregistrée mais le paiement n'a pas abouti.
              Vous pouvez effectuer une nouvelle tentative depuis votre espace client.
            </p>
            {erreur && (
              <p className="text-sm bg-gray-50 p-2 rounded">
                Raison : {erreur}
              </p>
            )}
          </div>

          <div className="flex justify-center gap-4 pt-4">
            <Button variant="outline" asChild>
              <a href="/">Retour à l'accueil</a>
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
