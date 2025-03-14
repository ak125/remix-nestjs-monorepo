import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData, useSearchParams } from "@remix-run/react";
import { useEffect, useRef } from "react";

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const category = url.searchParams.get("category");
  const tags = url.searchParams.getAll("tag");
  const search = url.searchParams.get("q");

  const [content, categories] = await Promise.all([
    fetch(
      `${process.env.API_URL}/ml?${new URLSearchParams({
        ...(category && { category }),
        ...(tags.length && { tags: tags.join(",") }),
        ...(search && { search })
      })}`
    ),
    fetch(`${process.env.API_URL}/ml/categories`)
  ]);

  return json({
    content: await content.json(),
    categories: await categories.json()
  });
}

export default function MLIndex() {
  const { content, categories } = useLoaderData<typeof loader>();
  const [searchParams, setSearchParams] = useSearchParams();
  const searchRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    const timer = setTimeout(() => {
      const search = searchRef.current?.value;
      if (search) {
        setSearchParams(prev => {
          prev.set("q", search);
          return prev;
        });
      }
    }, 300);

    return () => clearTimeout(timer);
  }, [searchRef.current?.value]);

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-4xl font-bold mb-8">Machine Learning</h1>

      <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div className="md:col-span-1">
          <div className="bg-white rounded-lg shadow p-6 space-y-6">
            <div>
              <h3 className="text-lg font-semibold mb-4">Rechercher</h3>
              <input
                ref={searchRef}
                type="text"
                defaultValue={searchParams.get("q") || ""}
                placeholder="Rechercher..."
                className="w-full border rounded px-3 py-2"
              />
            </div>

            <div>
              <h3 className="text-lg font-semibold mb-4">Catégories</h3>
              <div className="space-y-2">
                {categories.map(category => (
                  <button
                    key={category.name}
                    onClick={() => setSearchParams({ category: category.name })}
                    className={`block w-full text-left px-3 py-2 rounded ${
                      searchParams.get("category") === category.name
                        ? "bg-blue-50 text-blue-700"
                        : "hover:bg-gray-50"
                    }`}
                  >
                    {category.name} ({category.count})
                  </button>
                ))}
              </div>
            </div>
          </div>
        </div>

        <div className="md:col-span-3">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {content.map(item => (
              <div key={item.id} className="bg-white rounded-lg shadow overflow-hidden">
                {item.imageUrl && (
                  <img
                    src={item.imageUrl}
                    alt={item.title}
                    className="w-full h-48 object-cover"
                  />
                )}
                <div className="p-6">
                  <h2 className="text-xl font-bold mb-2">{item.title}</h2>
                  <p className="text-gray-600 mb-4">{item.description}</p>
                  <div className="flex flex-wrap gap-2">
                    {item.tags.map(tag => (
                      <span
                        key={tag}
                        className="px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded-full"
                      >
                        {tag}
                      </span>
                    ))}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
