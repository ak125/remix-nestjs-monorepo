import { json, LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData, useFetcher } from "@remix-run/react";
import { prisma } from "~/lib/prisma.server";
import { typedjson } from "remix-typedjson";

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const query = url.searchParams;

  // Parse params
  const params = {
    marqueId: Number(query.get("marqueId")) || undefined,
    yearFrom: Number(query.get("yearFrom")) || undefined,
    yearTo: Number(query.get("yearTo")) || undefined,
    modelId: Number(query.get("modelId")) || undefined
  };

  try {
    const [types, totalCount] = await Promise.all([
      // Get types with filters
      prisma.autoType.findMany({
        where: {
          TYPE_DISPLAY: true,
          ...(params.marqueId && { TYPE_MARQUE_ID: params.marqueId }),
          ...(params.modelId && { TYPE_MODELE_ID: params.modelId }),
          ...(params.yearFrom && { TYPE_YEAR_FROM: { gte: params.yearFrom } }),
          ...(params.yearTo && { TYPE_YEAR_TO: { lte: params.yearTo } })
        },
        include: {
          AUTO_MODELE: {
            include: {
              AUTO_MARQUE: true
            }
          }
        },
        orderBy: {
          TYPE_SORT: 'asc'
        },
        take: 24
      }),

      // Get total count
      prisma.autoType.count({
        where: {
          TYPE_DISPLAY: true,
          ...(params.marqueId && { TYPE_MARQUE_ID: params.marqueId }),
          ...(params.modelId && { TYPE_MODELE_ID: params.modelId }),
          ...(params.yearFrom && { TYPE_YEAR_FROM: { gte: params.yearFrom } }),
          ...(params.yearTo && { TYPE_YEAR_TO: { lte: params.yearTo } })
        }
      })
    ]);

    // Cache for 1 hour
    return typedjson(
      { 
        types,
        totalCount,
        params
      },
      {
        headers: {
          'Cache-Control': 'public, max-age=3600'
        }
      }
    );

  } catch (error) {
    console.error('Search error:', error);
    throw new Response("Error searching cars", { status: 500 });
  }
}

export default function SearchPage() {
  const { types, totalCount, params } = useLoaderData<typeof loader>();
  const fetcher = useFetcher();

  const isLoading = fetcher.state === "submitting";

  return (
    <div className="container mx-auto py-8 px-4">
      {/* Search filters */}
      <div className="mb-8">
        <fetcher.Form method="get" className="flex gap-4">
          {/* ... filters ... */}
        </fetcher.Form>
      </div>

      {/* Results count */}
      <p className="text-sm text-gray-500 mb-4">
        {totalCount} résultat{totalCount > 1 ? 's' : ''}
      </p>

      {/* Results grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {types.map(type => (
          <div key={type.TYPE_ID} className="bg-white rounded-lg shadow p-4">
            {/* ... result card ... */}
          </div>
        ))}
      </div>
    </div>
  );
}
