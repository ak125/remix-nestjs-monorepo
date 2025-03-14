import { LoaderFunctionArgs, ActionFunctionArgs, json } from "@remix-run/node";
import { useLoaderData, useFetcher } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatDate } from "~/lib/utils";
import { useRef, useEffect } from "react";
import { Chart } from "chart.js/auto";

export async function loader({ request }: LoaderFunctionArgs) {
  await requireUserId(request);
  
  const url = new URL(request.url);
  const page = Number(url.searchParams.get("page")) || 1;
  const filterNote = url.searchParams.get("note");
  const orderBy = url.searchParams.get("orderBy") || "date";

  const [avis, stats] = await Promise.all([
    fetch(
      `${process.env.API_URL}/avis?page=${page}&orderBy=${orderBy}${
        filterNote ? `&note=${filterNote}` : ""
      }`
    ),
    fetch(`${process.env.API_URL}/avis/stats`)
  ]);

  return json({
    avis: await avis.json(),
    stats: await stats.json()
  });
}

export async function action({ request }: ActionFunctionArgs) {
  const formData = await request.formData();
  const { avisId } = Object.fromEntries(formData);

  await fetch(
    `${process.env.API_URL}/avis/${avisId}`,
    { method: "DELETE" }
  );

  return null;
}

export default function AdminAvisPage() {
  const { avis, stats } = useLoaderData<typeof loader>();
  const fetcher = useFetcher();
  const chartRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    if (!chartRef.current || !stats?.distribution) return;

    const ctx = chartRef.current.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: Object.keys(stats.distribution),
        datasets: [{
          label: 'Nombre d\'avis',
          data: Object.values(stats.distribution),
          backgroundColor: 'rgba(59, 130, 246, 0.5)'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: `Note moyenne : ${stats.average.toFixed(1)} / 5`
          }
        }
      }
    });
  }, [stats]);

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">Gestion des Avis</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div className="bg-white p-6 rounded-lg shadow">
          <canvas ref={chartRef}></canvas>
        </div>

        <div className="bg-white p-6 rounded-lg shadow">
          <h2 className="text-xl font-semibold mb-4">Statistiques</h2>
          <p>Total des avis : {stats.total}</p>
          <p>Note moyenne : {stats.average.toFixed(1)}/5</p>
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
                Nom
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Note
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Commentaire
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {avis.map((avis: any) => (
              <tr key={avis.id}>
                <td className="px-6 py-4 whitespace-nowrap">
                  {formatDate(avis.createdAt)}
                </td>
                <td className="px-6 py-4">{avis.nom}</td>
                <td className="px-6 py-4">{"⭐".repeat(avis.note)}</td>
                <td className="px-6 py-4">
                  <p className="line-clamp-2">{avis.commentaire}</p>
                </td>
                <td className="px-6 py-4">
                  <button
                    onClick={() => {
                      if (window.confirm("Supprimer cet avis ?")) {
                        fetcher.submit(
                          { avisId: avis.id },
                          { method: "delete" }
                        );
                      }
                    }}
                    className="text-red-600 hover:text-red-800"
                  >
                    Supprimer
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
