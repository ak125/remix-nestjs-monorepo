import { ActionFunctionArgs, LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";

export async function loader({ request, params }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/orders/${params.orderId}/lines`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Commande non trouvée", { status: 404 });
  }

  return json({
    lines: await response.json(),
    userId
  });
}

export async function action({ request, params }: ActionFunctionArgs) {
  const userId = await requireUserId(request);
  const formData = await request.formData();

  const response = await fetch(
    `${process.env.API_URL}/orders/${params.orderId}/lines/${formData.get('lineId')}/status`,
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
    return json({ error: "Mise à jour échouée" });
  }

  return null;
}

export default function OrderLineStatusPage() {
  const { lines } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="bg-white rounded-lg shadow-lg overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Référence
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Quantité
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Statut
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {lines.map(line => (
              <tr key={line.id}>
                <td className="px-6 py-4">
                  {line.product.reference}
                </td>
                <td className="px-6 py-4 text-right">
                  {line.quantity}
                </td>
                <td className="px-6 py-4 text-right">
                  <StatusBadge status={line.status} />
                </td>
                <td className="px-6 py-4 text-right">
                  <Form method="post" className="inline-flex gap-2">
                    <input type="hidden" name="lineId" value={line.id} />
                    <select 
                      name="status"
                      className="border rounded px-2 py-1"
                      defaultValue={line.status}
                    >
                      <option value="pending">En attente</option>
                      <option value="processing">En cours</option>
                      <option value="shipped">Expédié</option>
                      <option value="delivered">Livré</option>
                      <option value="cancelled">Annulé</option>
                    </select>
                    <button
                      type="submit"
                      className="bg-blue-500 text-white px-3 py-1 rounded"
                    >
                      Mettre à jour
                    </button>
                  </Form>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

function StatusBadge({ status }: { status: string }) {
  const styles = {
    pending: "bg-yellow-100 text-yellow-800",
    processing: "bg-blue-100 text-blue-800",
    shipped: "bg-green-100 text-green-800",
    delivered: "bg-gray-100 text-gray-800",
    cancelled: "bg-red-100 text-red-800"
  };

  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
      styles[status as keyof typeof styles]
    }`}>
      {status}
    </span>
  );
}
