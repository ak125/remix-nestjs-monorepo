import { ActionFunctionArgs, LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { useState } from "react";

export async function loader({ request }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);
  
  const [stock, alerts] = await Promise.all([
    fetch(`${process.env.API_URL}/stock`),
    fetch(`${process.env.API_URL}/stock/low-stock`)
  ]);

  return json({
    items: await stock.json(),
    alerts: await alerts.json()
  });
}

export async function action({ request }: ActionFunctionArgs) {
  const userId = await requireUserId(request);
  const formData = await request.formData();
  
  const response = await fetch(
    `${process.env.API_URL}/stock/${formData.get('id')}/${formData.get('action')}`,
    {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        quantity: Number(formData.get('quantity')),
        reason: formData.get('reason'),
        userId
      })
    }
  );

  if (!response.ok) {
    return json({ error: "Erreur lors de la mise à jour" });
  }

  return null;
}

export default function StockAdminPage() {
  const { items, alerts } = useLoaderData<typeof loader>();
  const [selectedItem, setSelectedItem] = useState<any>(null);

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="mb-8">
        <h1 className="text-3xl font-bold mb-4">Gestion des Stocks</h1>

        {alerts.length > 0 && (
          <div className="bg-red-50 p-4 rounded-lg">
            <h2 className="text-red-800 font-semibold mb-2">
              Alertes Stock ({alerts.length})
            </h2>
            <ul className="space-y-2">
              {alerts.map(alert => (
                <li key={alert.id} className="text-red-600">
                  {alert.message}
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Référence
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nom
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Quantité
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Seuil Min
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {items.map(item => (
              <tr key={item.id}>
                <td className="px-6 py-4 whitespace-nowrap">
                  {item.reference}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {item.name}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                    item.quantity <= item.minQuantity
                      ? 'bg-red-100 text-red-800'
                      : 'bg-green-100 text-green-800'
                  }`}>
                    {item.quantity}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {item.minQuantity}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    onClick={() => setSelectedItem(item)}
                    className="text-blue-600 hover:text-blue-900"
                  >
                    Modifier
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {selectedItem && (
        <div className="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
          <div className="bg-white rounded-lg p-6 max-w-md w-full">
            <Form method="post" className="space-y-4">
              <input type="hidden" name="id" value={selectedItem.id} />
              
              <div>
                <label className="block text-sm font-medium text-gray-700">
                  Quantité
                </label>
                <input
                  type="number"
                  name="quantity"
                  defaultValue={selectedItem.quantity}
                  min="0"
                  className="mt-1 block w-full border rounded-md shadow-sm p-2"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700">
                  Raison
                </label>
                <textarea
                  name="reason"
                  className="mt-1 block w-full border rounded-md shadow-sm p-2"
                  rows={3}
                />
              </div>

              <div className="flex justify-end gap-3">
                <button
                  type="button"
                  onClick={() => setSelectedItem(null)}
                  className="px-4 py-2 text-sm text-gray-600"
                >
                  Annuler
                </button>
                <button
                  type="submit"
                  name="action"
                  value="update"
                  className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                  Enregistrer
                </button>
              </div>
            </Form>
          </div>
        </div>
      )}
    </div>
  );
}
