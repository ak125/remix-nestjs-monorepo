import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatDate } from "~/lib/utils";

export async function loader({ request }: LoaderFunctionArgs) {
  await requireUserId(request);
  
  const url = new URL(request.url);
  const language = url.searchParams.get("lang") || "fr";

  const response = await fetch(
    `${process.env.API_URL}/security/history?language=${language}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export default function SecurityHistoryPage() {
  const history = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">
        Historique des Modifications
      </h1>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Date
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Version
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Modifié par
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Type
              </th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {history.map(log => (
              <tr key={log.id}>
                <td className="px-6 py-4 whitespace-nowrap">
                  {formatDate(log.createdAt)}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  v{log.version}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  {log.userEmail}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 py-1 rounded-full text-xs ${
                    log.changeType === 'UPDATE' 
                      ? 'bg-blue-100 text-blue-800'
                      : 'bg-gray-100 text-gray-800'
                  }`}>
                    {log.changeType}
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
