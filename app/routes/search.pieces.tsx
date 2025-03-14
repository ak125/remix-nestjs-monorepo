import { LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useLoaderData, useSearchParams, useFetcher } from "@remix-run/react";
import { useEffect, useState } from "react";
import { Combobox } from '@headlessui/react';
import { debounce } from "~/utils/debounce";

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const term = url.searchParams.get('q');
  const brand = url.searchParams.get('brand');
  const inStock = url.searchParams.get('inStock');

  if (!term) {
    return json({ pieces: [], brands: await getBrands() });
  }

  const [pieces, suggestions] = await Promise.all([
    searchPieces({ term, brand, inStock: inStock === 'true' }),
    getSuggestions(term)
  ]);

  return json({ pieces, suggestions, brands: await getBrands() });
}

export default function SearchPage() {
  const { pieces, suggestions, brands } = useLoaderData<typeof loader>();
  const [searchParams, setSearchParams] = useSearchParams();
  const [query, setQuery] = useState(searchParams.get('q') || '');
  const fetcher = useFetcher();

  const debouncedSearch = debounce((value: string) => {
    setSearchParams({ q: value });
  }, 300);

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-4xl mx-auto">
        <Form className="mb-8">
          <div className="flex gap-4 mb-4">
            <div className="flex-1">
              <Combobox
                value={query}
                onChange={(value) => {
                  setQuery(value);
                  debouncedSearch(value);
                }}
              >
                <div className="relative">
                  <Combobox.Input
                    className="w-full border rounded-lg px-4 py-2"
                    placeholder="Rechercher une pièce..."
                  />
                  <Combobox.Options className="absolute w-full bg-white mt-1 rounded-lg shadow-lg">
                    {suggestions?.map(suggestion => (
                      <Combobox.Option
                        key={suggestion.reference}
                        value={suggestion.reference}
                        className={({ active }) =>
                          `p-2 cursor-pointer ${active ? 'bg-blue-50' : ''}`
                        }
                      >
                        {suggestion.reference} - {suggestion.name}
                      </Combobox.Option>
                    ))}
                  </Combobox.Options>
                </div>
              </Combobox>
            </div>

            <select
              name="brand"
              className="border rounded-lg px-4 py-2"
              onChange={e => setSearchParams({ brand: e.target.value })}
              value={searchParams.get('brand') || ''}
            >
              <option value="">Toutes marques</option>
              {brands.map(brand => (
                <option key={brand.id} value={brand.id}>
                  {brand.name}
                </option>
              ))}
            </select>

            <label className="flex items-center gap-2">
              <input
                type="checkbox"
                name="inStock"
                checked={searchParams.get('inStock') === 'true'}
                onChange={e => setSearchParams({ inStock: String(e.target.checked) })}
              />
              En stock uniquement
            </label>
          </div>
        </Form>

        <div className="space-y-4">
          {pieces.map(piece => (
            <div 
              key={piece.id} 
              className="bg-white rounded-lg shadow p-4"
            >
              <div className="flex justify-between">
                <div>
                  <h3 className="font-medium">{piece.name}</h3>
                  <p className="text-sm text-gray-500">
                    {piece.reference} - {piece.brand.name}
                  </p>
                </div>
                <div className="text-right">
                  <div className="font-medium">
                    {piece.price.toFixed(2)} €
                  </div>
                  <div className={
                    piece.stock > 0 ? 'text-green-600' : 'text-red-600'
                  }>
                    {piece.stock > 0 ? 'En stock' : 'Rupture'}
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
