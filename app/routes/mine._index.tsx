import { LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useLoaderData, useSearchParams } from "@remix-run/react";
import { useRef, useEffect } from "react";

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const query = url.searchParams.get("query") || "";
  const type = url.searchParams.get("type");
  const location = url.searchParams.get("location");
  
  const [mines, types, locations] = await Promise.all([
    fetch(
      `${process.env.API_URL}/mine/search?${new URLSearchParams({
        query,
        ...(type && { type }),
        ...(location && { location })
      })}`
    ),
    fetch(`${process.env.API_URL}/mine/types`),
    fetch(`${process.env.API_URL}/mine/locations`)
  ]);

  return json({
    mines: await mines.json(),
    types: await types.json(),
    locations: await locations.json()
  });
}

export default function MineSearch() {
  const { mines, types, locations } = useLoaderData<typeof loader>();
  const [searchParams, setSearchParams] = useSearchParams();
  const formRef = useRef<HTMLFormElement>(null);

  useEffect(() => {
    const timer = setTimeout(() => {
      if (formRef.current) formRef.current.submit();
    }, 300);

    return () => clearTimeout(timer);
  }, [searchParams.get("query")]);

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">Recherche de Mines</h1>

      <Form ref={formRef} method="get" className="space-y-6">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Recherche
            </label>
            <input
              type="text"
              name="query"
              defaultValue={searchParams.get("query") || ""}
              placeholder="Nom ou type de mine..."
              className="w-full border rounded px-3 py-2"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Type
            </label>
            <select
              name="type"
              defaultValue={searchParams.get("type") || ""}
              className="w-full border rounded px-3 py-2"
            >
              <option value="">Tous les types</option>
              {types.map(type => (
                <option key={type.name} value={type.name}>
                  {type.name} ({type.count})
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Localisation
            </label>
            <select
              name="location"
              defaultValue={searchParams.get("location") || ""}
              className="w-full border rounded px-3 py-2"
            >
              <option value="">Toutes les localisations</option>
              {locations.map(location => (
                <option key={location.name} value={location.name}>
                  {location.name} ({location.count})
                </option>
              ))}
            </select>
          </div>
        </div>
      </Form>

      <div className="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {mines.map(mine => (
          <div key={mine.id} className="bg-white rounded-lg shadow p-6">
            <h2 className="text-xl font-semibold mb-2">{mine.name}</h2>
            <p className="text-gray-600">Type: {mine.type}</p>
            <p className="text-gray-600">Localisation: {mine.location}</p>
            {mine.description && (
              <p className="mt-2 text-sm text-gray-500">{mine.description}</p>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
