import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData, useSearchParams } from "@remix-run/react";
import { SearchResults } from "~/components/search/SearchResults";
import { SearchFilters } from "~/components/search/SearchFilters";
import { SearchBar } from "~/components/search/SearchBar";

interface LoaderData {
  pieces: any[];
  filters: {
    equipements: any[];
    gammes: any[];
  };
  pagination: {
    total: number;
    pages: number; 
    page: number;
    limit: number;
  };
}

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const query = url.searchParams.get("q") || "";
  const page = parseInt(url.searchParams.get("page") || "1", 10);
  const equipementId = url.searchParams.get("equipement");
  const gammeId = url.searchParams.get("gamme");

  const [results, filters] = await Promise.all([
    fetch(`${process.env.API_URL}/search?${new URLSearchParams({
      query,
      page: String(page),
      ...(equipementId && { equipementId }),
      ...(gammeId && { gammeId })
    })}`).then(r => r.json()),
    fetch(`${process.env.API_URL}/search/filters?query=${query}`).then(r => r.json())
  ]);

  return json<LoaderData>({ 
    ...results,
    filters 
  });
};

export default function SearchPage() {
  const { pieces, filters, pagination } = useLoaderData<LoaderData>();
  const [searchParams] = useSearchParams();
  const query = searchParams.get("q") || "";

  return (
    <div className="container mx-auto p-4">
      <SearchBar defaultValue={query} />

      <div className="flex gap-6 mt-8">
        <aside className="w-64 flex-shrink-0">
          <SearchFilters 
            filters={filters}
            currentEquipement={searchParams.get("equipement")}
            currentGamme={searchParams.get("gamme")}
          />
        </aside>

        <main className="flex-1">
          <SearchResults 
            pieces={pieces}
            pagination={pagination}
          />
        </main>
      </div>
    </div>
  );
}
