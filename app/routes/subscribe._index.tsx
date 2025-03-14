import { ActionFunctionArgs, LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { useState } from "react";

const PLANS = [
  { id: "price_monthly", name: "Mensuel", price: 9.99 },
  { id: "price_yearly", name: "Annuel", price: 99.99 }
];

export async function loader({ request }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/billing/customer/${userId}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export async function action({ request }: ActionFunctionArgs) {
  const formData = await request.formData();
  const priceId = formData.get("priceId");

  const response = await fetch(
    `${process.env.API_URL}/billing/subscribe`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ priceId })
    }
  );

  if (!response.ok) {
    throw new Response("Erreur lors de la souscription", { status: 500 });
  }

  const { clientSecret } = await response.json();
  return json({ clientSecret });
}

export default function SubscribePage() {
  const { customer } = useLoaderData<typeof loader>();
  const [selectedPlan, setSelectedPlan] = useState(PLANS[0]);

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">Choisir un abonnement</h1>

        <div className="grid md:grid-cols-2 gap-6 mb-8">
          {PLANS.map(plan => (
            <div 
              key={plan.id}
              className={`p-6 rounded-lg border-2 cursor-pointer ${
                selectedPlan.id === plan.id 
                  ? 'border-blue-500 bg-blue-50'
                  : 'border-gray-200'
              }`}
              onClick={() => setSelectedPlan(plan)}
            >
              <h3 className="text-xl font-semibold mb-2">{plan.name}</h3>
              <p className="text-3xl font-bold mb-4">{plan.price}€</p>
              <ul className="space-y-2 text-gray-600">
                <li>✓ Toutes les fonctionnalités</li>
                <li>✓ Support prioritaire</li>
                <li>✓ Mises à jour incluses</li>
              </ul>
            </div>
          ))}
        </div>

        <form method="post" className="text-center">
          <input type="hidden" name="priceId" value={selectedPlan.id} />
          <button
            type="submit"
            className="bg-blue-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-600"
          >
            S'abonner maintenant
          </button>
        </form>
      </div>
    </div>
  );
}
