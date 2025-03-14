import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { getUserSession } from "~/server/auth.server";

export const loader: LoaderFunction = async ({ request }) => {
  const user = await getUserSession(request);
  if (!user) {
    throw new Response("Unauthorized", { status: 401 });
  }

  const res = await fetch(`${process.env.API_URL}/orders/${user.id}`);
  if (!res.ok) throw new Response("Erreur lors du chargement des commandes", { status: 500 });

  const orders = await res.json();
  return json({ user, orders });
};

export default function OrdersPage() {
  const { orders } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-6">
      <h1 className="text-2xl font-bold mb-6">Mes Commandes</h1>

      <div className="bg-white shadow-md rounded-lg p-4">
        {orders.length === 0 ? (
          <p className="text-gray-500">Vous n'avez aucune commande pour le moment.</p>
        ) : (
          <table className="w-full border-collapse">
            <thead>
              <tr className="border-b">
                <th className="p-2 text-left">Commande</th>
                <th className="p-2 text-left">Date</th>
                <th className="p-2 text-left">Total</th>
                <th className="p-2 text-left">Statut</th>
              </tr>
            </thead>
            <tbody>
              {orders.map((order: any) => (
                <tr key={order.ORD_ID} className="border-b">
                  <td className="p-2">{order.ORD_ID}/A</td>
                  <td className="p-2">{new Date(order.ORD_DATE).toLocaleDateString()}</td>
                  <td className="p-2">{order.ORD_TOTAL_TTC.toFixed(2)} €</td>
                  <td className="p-2">{order.ORD_IS_PAY ? "Payé" : "En attente de paiement"}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
