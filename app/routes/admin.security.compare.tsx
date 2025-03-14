import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData, useSearchParams } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { formatDate } from "~/lib/utils";
import { useRef, useEffect } from "react";

export async function loader({ request }: LoaderFunctionArgs) {
  await requireUserId(request);

  const url = new URL(request.url);
  const v1 = url.searchParams.get("v1");
  const v2 = url.searchParams.get("v2");

  const archives = await fetch(
    `${process.env.API_URL}/security/archives`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!archives.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  let comparison = null;
  if (v1 && v2) {
    const compareResponse = await fetch(
      `${process.env.API_URL}/security/compare?v1=${v1}&v2=${v2}`,
      { headers: { 'Cache-Control': 'no-cache' } }
    );
    
    if (compareResponse.ok) {
      comparison = await compareResponse.json();
    }
  }

  return json({
    archives: await archives.json(),
    comparison
  });
}

export default function SecurityComparePage() {
  const { archives, comparison } = useLoaderData<typeof loader>();
  const [searchParams, setSearchParams] = useSearchParams();
  const downloadLinkRef = useRef<HTMLAnchorElement>(null);

  const handleExportCSV = () => {
    if (downloadLinkRef.current) {
      downloadLinkRef.current.click();
    }
  };

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold">Comparaison des Versions</h1>
        <button
          onClick={handleExportCSV}
          className="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
        >
          Exporter CSV
        </button>
        <a
          ref={downloadLinkRef}
          href={`${process.env.API_URL}/security/export/csv`}
          className="hidden"
        />
      </div>

      <div className="grid grid-cols-2 gap-6 mb-8">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Version 1
          </label>
          <select
            value={searchParams.get("v1") || ""}
            onChange={e => setSearchParams({ 
              v1: e.target.value,
              v2: searchParams.get("v2") || ""
            })}
            className="w-full border rounded px-3 py-2"
          >
            <option value="">Sélectionner une version</option>
            {archives.map(archive => (
              <option key={archive.id} value={archive.id}>
                Version {archive.version} ({formatDate(archive.createdAt)})
              </option>
            ))}
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Version 2
          </label>
          <select
            value={searchParams.get("v2") || ""}
            onChange={e => setSearchParams({ 
              v1: searchParams.get("v1") || "",
              v2: e.target.value
            })}
            className="w-full border rounded px-3 py-2"
          >
            <option value="">Sélectionner une version</option>
            {archives.map(archive => (
              <option key={archive.id} value={archive.id}>
                Version {archive.version} ({formatDate(archive.createdAt)})
              </option>
            ))}
          </select>
        </div>
      </div>

      {comparison && (
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold mb-4">Différences</h2>
          <div className="space-y-2">
            {comparison.differences.map((diff, index) => (
              <pre
                key={index}
                className={`p-2 rounded ${
                  diff.type === 'added' 
                    ? 'bg-green-50 text-green-800'
                    : diff.type === 'removed'
                    ? 'bg-red-50 text-red-800'
                    : 'bg-gray-50'
                }`}
              >
                {diff.type === 'added' ? '+ ' : diff.type === 'removed' ? '- ' : '  '}
                {diff.value}
              </pre>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
