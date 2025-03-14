import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData, useSearchParams } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatDate, formatCurrency } from "~/lib/utils";
import { Chart } from "chart.js/auto";
import { useEffect, useRef, useState } from "react";

export async function loader({ request }: LoaderFunctionArgs) {
  await requireUserId(request);

  const url = new URL(request.url);
  const startDate = url.searchParams.get("startDate") || 
    new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
  const endDate = url.searchParams.get("endDate") || 
    new Date().toISOString().split('T')[0];

  const response = await fetch(
    `${process.env.API_URL}/admin/stats?startDate=${startDate}&endDate=${endDate}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export default function AdminDashboardPage() {
  const { stats, monthlyData } = useLoaderData<typeof loader>();
  const [searchParams, setSearchParams] = useSearchParams();
  const chartRef = useRef<HTMLCanvasElement>(null);

  const [startDate, setStartDate] = useState(
    searchParams.get("startDate") || 
    new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
  );
  const [endDate, setEndDate] = useState(
    searchParams.get("endDate") || 
    new Date().toISOString().split('T')[0]
  );

  useEffect(() => {
    if (!chartRef.current || !monthlyData) return;

    const ctx = chartRef.current.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: monthlyData.map(d => formatDate(d.month)),
        datasets: [
          {
            label: 'Revenus (€)',
            data: monthlyData.map(d => d.total),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true
          },
          {
            label: 'Remboursements (€)',
            data: monthlyData.map(d => d.refunds),
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            fill: true
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Évolution des revenus'
          },
          legend: {
            position: 'top'
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }
    });
  }, [monthlyData]);

  const updateDateRange = () => {
    setSearchParams({ startDate, endDate });
  };

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">Tableau de Bord</h1>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-white p-6 rounded-lg shadow">
          <h3 className="text-lg font-semibold mb-2">Total des revenus</h3>
          <p className="text-3xl text-blue-600">
            {formatCurrency(stats.total)}
          </p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow">
          <h3 className="text-lg font-semibold mb-2">En attente</h3>
          <p className="text-3xl text-yellow-600">
            {formatCurrency(stats.pending)}
          </p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow">
          <h3 className="text-lg font-semibold mb-2">Remboursements</h3>
          <p className="text-3xl text-red-600">
            {formatCurrency(stats.refunded)}
          </p>
        </div>
      </div>

      <div className="bg-white p-6 rounded-lg shadow mb-8">
        <div className="flex gap-4 mb-4">
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
          <button
            onClick={updateDateRange}
            className="mt-6 bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600"
          >
            Filtrer
          </button>
        </div>
        <canvas ref={chartRef}></canvas>
      </div>
    </div>
  );
}
