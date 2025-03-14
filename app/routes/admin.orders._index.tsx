import { ActionFunctionArgs, LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useLoaderData, useSearchParams, useNavigation } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { useState } from "react";

export async function loader({ request }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);
  const url = new URL(request.url);
  
  const filters = {
    status: url.searchParams.get("status"),
    department: url.searchParams.get("department"),
    search: url.searchParams.get("search")
  };

  const response = await fetch(
    `${process.env.API_URL}/orders?${new URLSearchParams(filters as any)}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export default function OrdersAdminPage() {
  const orders = useLoaderData<typeof loader>();
  const navigation = useNavigation();
  const [searchParams, setSearchParams] = useSearchParams();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold">
          Gestion des Commandes
        </h1>

        <Form className="flex items-center gap-4">
          <select
            name="status"
            defaultValue={searchParams.get("status") || ""}
            onChange={e => setSearchParams({ status: e.target.value })}
            className="border rounded px-3 py-2"
          >
            <option value="">Tous les statuts</option>
            <option value="pending">En attente</option>
            <option value="processing">En cours</option>
            <option value="shipped">Expédiée</option>
          </select>

          <select
            name="department" 
            defaultValue={searchParams.get("department") || ""}
            onChange={e => setSearchParams({ department: e.target.value })}
            className="border rounded px-3 py-2"
          >
            <option value="">Tous les services</option>
            <option value="commercial">Commercial</option>
            <option value="shipping">Expédition</option>
          </select>

          <input
            type="search"
            name="search"
            placeholder="Rechercher..."
            defaultValue={searchParams.get("search") || ""}
            className="border rounded px-3 py-2"
          />
        </Form>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            {/* Table headers omitted for brevity */}
            <tbody className="divide-y divide-gray-200">
              {orders.map(order => (
                <tr key={order.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="font-medium">{order.orderId}</div>
                    <div className="text-sm text-gray-500">
                      {new Date(order.dateCreated).toLocaleDateString()}
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <div>{order.customer.name}</div>
                    <div className="text-sm text-gray-500">
                      {order.customer.email}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    {order.totalAmount.toFixed(2)} €
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                      order.status === 'shipped' 
                        ? 'bg-green-100 text-green-800'
                        : 'bg-yellow-100 text-yellow-800'
                    }`}>
                      {order.status}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a 
                      href={`/admin/orders/${order.id}`}
                      className="text-blue-600 hover:text-blue-900"
                    >
                      Détails
                    </a>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
