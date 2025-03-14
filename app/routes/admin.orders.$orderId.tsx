import { ActionFunctionArgs, LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatCurrency } from "~/utils/format";

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
    `${process.env.API_URL}/orders/${params.orderId}/status`,
    {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        status: formData.get('status'),
        userId
      })
    }
  );

  if (!response.ok) {
    return json({ error: "Erreur lors de la mise à jour" });
  }

  return null;
}

export default function OrderDetailsPage() {
  const order = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="bg-white rounded-lg shadow-lg overflow-hidden p-6">
        <div className="flex justify-between items-start mb-6">
          <div>
            <h1 className="text-2xl font-bold">
              Commande #{order.orderId}
            </h1>
            <p className="text-gray-500">
              {new Date(order.createdAt).toLocaleDateString()}
            </p>
          </div>

          <Form method="post" className="flex items-center gap-2">
            <select 
              name="status"
              defaultValue={order.status}
              className="border rounded px-3 py-2"
            >
              <option value="pending">En attente</option>
              <option value="processing">En cours</option>
              <option value="shipped">Expédiée</option>
              <option value="delivered">Livrée</option>
              <option value="cancelled">Annulée</option>
            </select>

            <button
              type="submit"
              className="bg-blue-500 text-white px-4 py-2 rounded"
            >
              Mettre à jour
            </button>
          </Form>
        </div>

        {/* Order details table */}
        <table className="min-w-full divide-y divide-gray-200 mb-6">
          <thead>
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Produit
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Prix unitaire
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Quantité
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {order.items.map(item => (
              <tr key={item.id}>
                <td className="px-6 py-4 whitespace-nowrap">
                  {item.productName}
                </td>
                <td className="px-6 py-4 text-right">
                  {formatCurrency(item.unitPrice)}
                </td>
                <td className="px-6 py-4 text-right">
                  {item.quantity}
                </td>
                <td className="px-6 py-4 text-right">
                  {formatCurrency(item.totalPrice)}
                </td>
              </tr>
            ))}
          </tbody>
          <tfoot>
            <tr>
              <td colSpan={3} className="px-6 py-4 text-right font-bold">
                Total
              </td>
              <td className="px-6 py-4 text-right font-bold">
                {formatCurrency(order.total)}
              </td>
            </tr>
          </tfoot>
        </table>

        {/* Order history */}
        <div className="border-t pt-6">
          <h2 className="text-lg font-semibold mb-4">Historique</h2>
          <div className="space-y-4">
            {order.history.map(event => (
              <div key={event.id} className="flex gap-4">
                <div className="text-sm text-gray-500">
                  {new Date(event.createdAt).toLocaleString()}
                </div>
                <div>{event.action}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
