import { json, LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData, useFetcher, useNavigation } from "@remix-run/react";
import { Select } from "~/components/ui/select";
import { Button } from "~/components/ui/button";
import { Loader2 } from "lucide-react";

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const brandId = url.searchParams.get("brandId");
  const gammeId = url.searchParams.get("gammeId");

  if (!brandId || !gammeId) {
    return json({ years: [] });
  }

  try {
    const response = await fetch(
      `${process.env.API_URL}/cars/years?brandId=${brandId}&gammeId=${gammeId}`,
      { headers: { 'Cache-Control': 'max-age=3600' } }
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const years = await response.json();
    return json({ years, error: null });

  } catch (error) {
    console.error('Failed to fetch years:', error);
    return json({ 
      years: [],
      error: "Impossible de charger les années. Veuillez réessayer."
    });
  }
}

export default function YearsPage() {
  const { years, error } = useLoaderData<typeof loader>();
  const navigation = useNavigation();
  const fetcher = useFetcher();

  const isLoading = navigation.state === "loading" || 
                   fetcher.state === "submitting";

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-md mx-auto">
        <h1 className="text-2xl font-bold mb-6">
          Sélectionner l'année
        </h1>

        <div className="space-y-4">
          {isLoading ? (
            <div className="flex items-center justify-center py-4">
              <Loader2 className="h-6 w-6 animate-spin" />
            </div>
          ) : error ? (
            <div className="p-4 border border-red-200 bg-red-50 text-red-600 rounded-lg">
              {error}
            </div>
          ) : (
            <Select.Root>
              <Select.Trigger className="w-full">
                <Select.Value placeholder="Choisissez une année" />
              </Select.Trigger>
              <Select.Content>
                {years.map((year: number) => (
                  <Select.Item key={year} value={year.toString()}>
                    {year}
                  </Select.Item>
                ))}
              </Select.Content>
            </Select.Root>
          )}

          <Button 
            type="submit"
            className="w-full"
            disabled={isLoading || !years.length}
          >
            {isLoading ? (
              <Loader2 className="h-4 w-4 mr-2 animate-spin" />
            ) : null}
            Valider
          </Button>
        </div>
      </div>
    </div>
  );
}
