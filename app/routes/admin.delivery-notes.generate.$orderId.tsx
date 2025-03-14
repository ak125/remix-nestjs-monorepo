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
    `${process.env.API_URL}/delivery-notes/generate`,
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        orderId: params.orderId,
        paymentStatus: formData.get('paymentStatus') === 'true',
        userId
      })
    }
  );

  if (!response.ok) {
    const error = await response.json();
    return json({ error: error.message });
  }

  const deliveryNote = await response.json();
  return redirect(`/admin/delivery-notes/${deliveryNote.id}`);
}

export default function GenerateDeliveryNotePage() {
  const order = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 className="text-2xl font-bold mb-6">
          Générer un Bon de Livraison
        </h1>

        <div className="mb-6">
          <h2 className="font-semibold mb-2">Détails de la commande</h2>
          <div className="space-y-2 text-sm">
            <p>Numéro: {order.orderNumber}</p>
            <p>Client: {order.customer.name}</p>
            <p>Total: {order.total.toFixed(2)} €</p>
          </div>
        </div>

        <Form method="post" className="space-y-6">
          <div>
            <label className="font-medium block mb-1">
              État du paiement
            </label>
            <select 
              name="paymentStatus"
              className="w-full border rounded-md p-2"
            >
              <option value="false">En attente de paiement</option>
              <option value="true">Payé</option>
            </select>
          </div>

          <div className="flex justify-end">
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
  );
}
