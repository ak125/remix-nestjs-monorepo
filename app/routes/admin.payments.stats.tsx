import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatDate, formatCurrency } from "~/lib/utils";
import { useEffect, useRef, useState } from "react";
import { Chart } from "chart.js/auto";

export async function loader({ request }: LoaderFunctionArgs) {
  await requireUserId(request);
  
  const url = new URL(request.url);
  const startDate = url.searchParams.get("startDate") || 
    new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
  const endDate = url.searchParams.get("endDate") || 
    new Date().toISOString().split('T')[0];

  const response = await fetch(
    `${process.env.API_URL}/payments/stats?startDate=${startDate}&endDate=${endDate}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export default function PaymentsStatsPage() {
  const data = useLoaderData<typeof loader>();
  const [startDate, setStartDate] = useState(
    new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
  );
  const [endDate, setEndDate] = useState(
    new Date().toISOString().split('T')[0]
  );
  const chartRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    if (!chartRef.current || !data.payments?.length) return;

    const ctx = chartRef.current.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.payments.map(p => formatDate(p.date)),
        datasets: [{
          label: 'Montant (€)',
          data: data.payments.map(p => p.amount),
          backgroundColor: 'rgba(59, 130, 246, 0.5)'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: { display: true, text: 'Paiements par période' }
        }
      }
    });
  }, [data.payments]);

  const handleRefund = async (paymentId: string) => {
    const ok = window.confirm("Confirmer le remboursement ?");
    if (!ok) return;

    const response = await fetch(`${process.env.API_URL}/payments/refund`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ paymentId })
    });

    if (response.ok) {
      window.location.reload();
    }
  };

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold">Statistiques des Paiements</h1>
        <button
          onClick={() => window.open(`${process.env.API_URL}/payments/excel`, "_blank")}
          className="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
        >
          Exporter Excel
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div className="bg-white p-6 rounded-lg shadow">
          <h3 className="text-lg font-semibold mb-2">Période</h3>
          <div className="flex gap-4">
            <div>
              <label className="block text-sm mb-1">De:</label>
              <input
                type="date"
                value={startDate}
                onChange={e => setStartDate(e.target.value)}
                className="border rounded px-2 py-1"
              />
            </div>
            <div>
              <label className="block text-sm mb-1">À:</label>
              <input
                type="date"
                value={endDate}
                onChange={e => setEndDate(e.target.value)}
                className="border rounded px-2 py-1"
              />
            </div>
          </div>
        </div>

        <div className="bg-white p-6 rounded-lg shadow">
          <h3 className="text-lg font-semibold mb-2">Total</h3>
          <p className="text-3xl">
            {formatCurrency(data.stats.totalAmount)}
          </p>
        </div>
      </div>

      <div className="bg-white p-6 rounded-lg shadow mb-8">
        <canvas ref={chartRef}></canvas>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          {/* ...existing table code... */}
          <tbody>
            {data.payments.map(payment => (
              <tr key={payment.id}>
                <td className="px-6 py-4">{formatDate(payment.date)}</td>
                <td className="px-6 py-4">{formatCurrency(payment.amount)}</td>
                <td className="px-6 py-4">{payment.email}</td>
                <td className="px-6 py-4">
                  <span className={`px-2 py-1 rounded-full text-xs ${
                    payment.status === 'succeeded' 
                      ? 'bg-green-100 text-green-800'
                      : 'bg-gray-100 text-gray-800'
                  }`}>
                    {payment.status}
                  </span>
                </td>
                <td className="px-6 py-4 space-x-2">
                  <button
                    onClick={() => window.open(`${process.env.API_URL}/payments/${payment.id}/invoice`, "_blank")}
                    className="text-blue-600 hover:text-blue-800"
                  >
                    PDF
                  </button>
                  {payment.status === 'succeeded' && (
                    <button
                      onClick={() => handleRefund(payment.id)}
                      className="text-red-600 hover:text-red-800"
                    >
                      Rembourser
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
