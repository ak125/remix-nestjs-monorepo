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
    throw new Response("Order not found", { status: 404 });
  }

  return json(await response.json());
}

export async function action({ request, params }: ActionFunctionArgs) {
  const userId = await requireUserId(request);
  const formData = await request.formData();

  const response = await fetch(
    `${process.env.API_URL}/orders/${params.orderId}/lines/${formData.get('lineId')}`,
    {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        status: formData.get('action'),
        reason: formData.get('reason'),
        userId
      })
    }
  );

  if (!response.ok) {
    return json({ error: "Update failed" });
  }

  return null;
}

export default function OrderLinesPage() {
  const order = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Produit
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Quantité
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Prix
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Status
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {order.items.map(line => (
              <tr key={line.id}>
                <td className="px-6 py-4">
                  {line.product.name}
                </td>
                <td className="px-6 py-4">
                  {line.quantity}
                </td>
                <td className="px-6 py-4 text-right">
                  {line.totalPrice.toFixed(2)} €
                </td>
                <td className="px-6 py-4 text-right">
                  <StatusBadge status={line.status} />
                </td>
                <td className="px-6 py-4 text-right">
                  <Form method="post" className="inline-flex gap-2">
                    <input type="hidden" name="lineId" value={line.id} />
                    
                    <button
                      type="submit"
                      name="action"
                      value="received"
                      className="text-green-600 hover:text-green-900"
                      disabled={line.isReceived}
                    >
                      Reçu
                    </button>

                    <button
                      type="submit" 
                      name="action"
                      value="deleted"
                      className="text-red-600 hover:text-red-900"
                    >
                      Supprimer
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
    received: "bg-green-100 text-green-800",
    deleted: "bg-red-100 text-red-800"
  };

  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
      styles[status as keyof typeof styles]
    }`}>
      {status}
    </span>
  );
}
