import { json, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent, CardHeader } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import invariant from "tiny-invariant";

export async function loader({ params }: LoaderFunctionArgs) {
  const { slug } = params;
  invariant(slug, "Slug de marque requis");

  const response = await fetch(
    `${process.env.API_URL}/blog/marques/${slug}`,
    {
      headers: {
        'Cache-Control': 'public, max-age=300'
      }
    }
  );

  if (!response.ok) {
    throw new Response("Marque non trouvée", { 
      status: response.status 
    });
  }

  return json(await response.json());
}

export default function MarquePage() {
  const data = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <Card className="mb-8">
          <CardHeader>
            <div className="flex items-center gap-6">
              <LazyImage
                src={data.logo}
                alt={data.name}
                className="w-24 h-24 object-contain"
              />
              <div>
                <h1 className="text-3xl font-bold">{data.name}</h1>
                <p className="text-gray-600 mt-2">{data.description}</p>
              </div>
            </div>
          </CardHeader>
        </Card>

        {/* Modèles */}
        <div className="space-y-8">
          <h2 className="text-2xl font-semibold">Modèles disponibles</h2>
          
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {data.models.map((model) => (
              <Card key={model.id} className="group hover:shadow-lg transition">
                <div className="relative aspect-video">
                  <LazyImage
                    src={model.image}
                    alt={model.name}
                    className="object-cover rounded-t-lg"
                    blur
                  />
                </div>
                <CardContent className="p-4">
                  <h3 className="font-semibold text-lg mb-2">{model.name}</h3>
                  {model.yearRange && (
                    <p className="text-sm text-gray-500">
                      {model.yearRange}
                    </p>
                  )}
                  <a 
                    href={`/blog/marque/${data.alias}/${model.alias}`}
                    className="text-blue-600 hover:underline text-sm mt-3 block"
                  >
                    Voir les détails →
                  </a>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>

        {/* Modèles Populaires */}
        {data.popularModels?.length > 0 && (
          <div className="mt-12 space-y-8">
            <h2 className="text-2xl font-semibold">Modèles populaires</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {data.popularModels.map((model) => (
                <Card key={model.id}>
                  {/* ...similar to models card... */}
                </Card>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
