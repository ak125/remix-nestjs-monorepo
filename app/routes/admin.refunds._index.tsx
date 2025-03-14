import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatDate, formatCurrency } from "~/lib/utils";
import { useEffect, useRef } from "react";
import { Chart } from "chart.js/auto";

export async function loader({ request }: LoaderFunctionArgs) {
  await requireUserId(request);

  const url = new URL(request.url);
  const startDate = url.searchParams.get("startDate") || 
    new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
  const endDate = url.searchParams.get("endDate") || 
    new Date().toISOString().split('T')[0];

  const response = await fetch(
    `${process.env.API_URL}/payments/failed-stats?startDate=${startDate}&endDate=${endDate}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export default function RefundsPage() {
  const { daily, total } = useLoaderData<typeof loader>();
  const chartRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    if (!chartRef.current || !daily?.length) return;

    const ctx = chartRef.current.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: daily.map(d => formatDate(d.date)),
        datasets: [{
          label: 'Paiements échoués',
          data: daily.map(d => d.count),
          backgroundColor: 'rgba(239, 68, 68, 0.5)'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Paiements échoués par jour'
          }
        }
      }
    });
  }, [daily]);

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">Suivi des Paiements Échoués</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div className="bg-white p-6 rounded-lg shadow">
          <h3 className="text-lg font-semibold mb-2">Total</h3>
          <p className="text-3xl text-red-600">
            {total.count} échecs
          </p>
          <p className="text-gray-600">
            {formatCurrency(total.amount)} concernés
          </p>
        </div>
        
        <div className="bg-white p-6 rounded-lg shadow">
          <canvas ref={chartRef}></canvas>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Date
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nombre d'échecs
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Montant concerné
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {daily.map(day => (
              <tr key={day.date}>
                <td className="px-6 py-4 whitespace-nowrap">
                  {formatDate(day.date)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {day.count}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {formatCurrency(day.amount)}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
