import { LoaderFunctionArgs, ActionFunctionArgs, json } from "@remix-run/node";
import { useLoaderData, useFetcher } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatDate } from "~/lib/utils";
import { useRef, useEffect } from "react";
import { Chart } from "chart.js";

export async function loader({ request }: LoaderFunctionArgs) {
  // Vérifier que l'utilisateur est admin
  await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/devis/list`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export async function action({ request }: ActionFunctionArgs) {
  const formData = await request.formData();
  const { devisId, action, amount } = Object.fromEntries(formData);

  if (action === "payment") {
    const response = await fetch(
      `${process.env.API_URL}/devis/payment`,
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ devisId, amount: Number(amount) })
      }
    );

    const { sessionId } = await response.json();
    return json({ sessionId });
  }

  return null;
}

export default function AdminDevisPage() {
  const { devis, stats } = useLoaderData<typeof loader>();
  const fetcher = useFetcher();
  const chartRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    if (!chartRef.current || !stats?.byDevice) return;

    const ctx = chartRef.current.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: Object.keys(stats.byDevice),
        datasets: [{
          data: Object.values(stats.byDevice),
          backgroundColor: [
            'rgba(59, 130, 246, 0.5)',
            'rgba(16, 185, 129, 0.5)',
            'rgba(239, 68, 68, 0.5)'
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Répartition par type d\'appareil'
          }
        }
      }
    });
  }, [stats]);

  const handlePayment = (devisId: string) => {
    fetcher.submit(
      { devisId, action: "payment", amount: "500" },
      { method: "post" }
    );
  };

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold">Gestion des Devis</h1>
        <button
          onClick={() => window.open(`${process.env.API_URL}/devis/export/excel`, "_blank")}
          className="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
        >
          Exporter Excel
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div className="bg-white p-6 rounded-lg shadow">
          <canvas ref={chartRef}></canvas>
        </div>

        <div className="bg-white p-6 rounded-lg shadow">
          <h2 className="text-xl font-semibold mb-4">Statistiques</h2>
          <p>Total des demandes : {stats.total}</p>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nom
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Détails
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Appareil
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {devis.map((d: any) => (
              <tr key={d.id}>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {formatDate(d.createdAt)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {d.name}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {d.email}
                </td>
                <td className="px-6 py-4">
                  <p className="text-sm text-gray-900 line-clamp-2">
                    {d.details}
                  </p>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {d.device?.type} / {d.device?.os}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <a
                    href={`${process.env.API_URL}/devis/pdf/${d.id}`}
                    className="inline-block bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    PDF
                  </a>
                  <button
                    onClick={() => handlePayment(d.id)}
                    className="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600"
                  >
                    Payer 500€
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
