import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useLoaderData } from "@remix-run/react"; 
import { requireUserId } from "~/utils/session.server";

export async function loader({ request, params }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/orders/${params.orderId}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Commande non trouvée", { status: 404 });
  }

  return json(await response.json());
}

export async function action({ request, params }: ActionFunctionArgs) {
  const userId = await requireUserId(request);
  const formData = await request.formData();

  const response = await fetch(
    `${process.env.API_URL}/orders/${params.orderId}/delivery`,
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        isPaid: formData.get('isPaid') === 'true',
        userId
      })
    }
  );

  if (!response.ok) {
    return json({ error: "Erreur lors de la génération" });
  }

  const delivery = await response.json();
  return redirect(`/admin/deliveries/${delivery.id}`);
}

export default function GenerateDeliveryPage() {
  const order = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">
          Générer un Bon de Livraison
        </h1>

        <div className="bg-white rounded-lg shadow-lg p-6">
          <div className="mb-6">
            <h2 className="font-semibold mb-2">Détails de la commande</h2>
            <p>Numéro: {order.orderNumber}</p>
            <p>Client: {order.customer.name}</p>
            <p>Total: {order.total.toFixed(2)} €</p>
          </div>

          <Form method="post" className="space-y-6">
            <div>
              <label className="block text-sm font-medium mb-2">
                État du paiement
              </label>
              <select 
                name="isPaid"
                className="w-full border rounded-md p-2"
                defaultValue="false"
              >
                <option value="false">En attente de paiement</option>
                <option value="true">Payé</option>
              </select>
            </div>

            <div className="flex justify-end gap-3">
              <button
                type="submit"
                className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
              >
                Générer le BL
              </button>
            </div>
          </Form>
        </div>
      </div>
    </div>
  );
}
