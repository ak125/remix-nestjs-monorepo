import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatDate, formatCurrency } from "~/lib/utils";

export async function loader({ request }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/billing/history/${userId}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export default function ClientDashboard() {
  const { payments, subscription } = useLoaderData<typeof loader>();

  const handleCancelSubscription = async () => {
    if (!window.confirm("Êtes-vous sûr de vouloir annuler votre abonnement ?")) {
      return;
    }

    const response = await fetch(
      `${process.env.API_URL}/billing/cancel-subscription`,
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ subscriptionId: subscription?.id })
      }
    );

    if (response.ok) {
      window.location.reload();
    }
  };

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">Mon Tableau de Bord</h1>

      {subscription && (
        <div className="bg-white rounded-lg shadow p-6 mb-8">
          <h2 className="text-xl font-semibold mb-4">Mon Abonnement</h2>
          <div className="flex justify-between items-center">
            <div>
              <p className="text-gray-600">Plan: {subscription.plan}</p>
              <p className="text-gray-600">
                Prochain paiement: {formatDate(subscription.currentPeriodEnd)}
              </p>
            </div>
            <button
              onClick={handleCancelSubscription}
              className="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
            >
              Annuler l'abonnement
            </button>
          </div>
        </div>
      )}

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <h2 className="p-6 text-xl font-semibold border-b">
          Historique des Paiements
        </h2>
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Montant
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Statut
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {payments.map(payment => (
              <tr key={payment.id}>
                <td className="px-6 py-4 whitespace-nowrap">
                  {formatDate(payment.created)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {formatCurrency(payment.amount)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 py-1 rounded-full text-xs ${
                    payment.status === 'succeeded' 
                      ? 'bg-green-100 text-green-800'
                      : 'bg-gray-100 text-gray-800'
                  }`}>
                    {payment.status}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <a
                    href={`${process.env.API_URL}/billing/invoice/${payment.id}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-blue-600 hover:text-blue-800"
                  >
                    Télécharger PDF
                  </a>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
