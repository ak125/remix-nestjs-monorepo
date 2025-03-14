import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatDate, formatCurrency } from "~/lib/utils";
import { useEffect, useRef } from "react";
import { Chart } from "chart.js/auto";

export async function loader({ request }: LoaderFunctionArgs) {
  await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/payments/stats`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export default function PaymentsPage() {
  const { recentPayments, stats } = useLoaderData<typeof loader>();
  const chartRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    if (chartRef.current && recentPayments.length > 0) {
      const ctx = chartRef.current.getContext("2d");
      if (!ctx) return;

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: recentPayments.map(p => formatDate(p.date)),
          datasets: [{
            label: "Montant (€)",
            data: recentPayments.map(p => p.amount),
            backgroundColor: "rgba(59, 130, 246, 0.5)"
          }]
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: "Paiements récents"
            }
          }
        }
      });
    }
  }, [recentPayments]);

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">Tableau de bord des paiements</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div className="bg-white p-6 rounded-lg shadow">
          <h3 className="text-lg font-semibold mb-2">Total des paiements</h3>
          <p className="text-3xl">{formatCurrency(stats.totalAmount)}</p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow">
          <h3 className="text-lg font-semibold mb-2">Nombre de paiements</h3>
          <p className="text-3xl">{stats.count}</p>
        </div>
      </div>

      <div className="bg-white p-6 rounded-lg shadow mb-8">
        <canvas ref={chartRef}></canvas>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Date
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Montant
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Statut
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {recentPayments.map(payment => (
              <tr key={payment.id}>
                <td className="px-6 py-4 whitespace-nowrap text-sm">
                  {formatDate(payment.date)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm">
                  {formatCurrency(payment.amount)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 py-1 text-xs rounded-full ${
                    payment.status === "succeeded" 
                      ? "bg-green-100 text-green-800"
                      : "bg-gray-100 text-gray-800"
                  }`}>
                    {payment.status}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
