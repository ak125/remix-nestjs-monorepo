import { json, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";
import invariant from "tiny-invariant";

export async function loader({ params }: LoaderFunctionArgs) {
  const { marqueAlias, mdgAlias } = params;
  invariant(marqueAlias, "Marque requise");
  invariant(mdgAlias, "Modèle requis");

  const response = await fetch(
    `${process.env.API_URL}/blog/constructeurs/${marqueAlias}/${mdgAlias}`,
    {
      headers: {
        'Cache-Control': 'public, max-age=300'
      }
    }
  );

  if (!response.ok) {
    throw new Response("Modèle non trouvé", { status: 404 });
  }

  const data = await response.json();

  return json(data, {
    headers: {
      'Cache-Control': 'public, max-age=300'
    }
  });
}

export default function ConstructeurModelePage() {
  const { marque, model, motorisations, popularModels } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="mb-12">
          <div className="flex items-center gap-6 mb-8">
            <LazyImage
              src={marque.logo}
              alt={marque.name}
              className="w-20 h-20 object-contain"
            />
            <div>
              <h1 className="text-3xl font-bold">
                {marque.name} {model.name}
              </h1>
              <p className="text-gray-600 mt-2">
                {model.description}
              </p>
            </div>
          </div>

          <Card className="p-6">
            <p className="text-gray-700">{model.content}</p>
          </Card>
        </div>

        {/* Motorisations */}
        <div className="space-y-8">
          <h2 className="text-2xl font-semibold">Motorisations disponibles</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {motorisations.map((motorisation) => (
              <Card key={motorisation.id} className="hover:shadow-lg transition">
                <CardContent className="p-6">
                  <div className="space-y-4">
                    <h3 className="font-semibold">
                      {motorisation.name}
                    </h3>
                    <div className="text-sm text-gray-600 space-y-2">
                      <p>Puissance: {motorisation.powerPs} ch</p>
                      <p>Période: {motorisation.yearRange}</p>
                      <p>Carburant: {motorisation.fuel}</p>
                    </div>
                    <a 
                      href={motorisation.url}
                      className="text-blue-600 hover:underline text-sm block"
                    >
                      Voir les pièces disponibles →
                    </a>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>

        {/* Modèles populaires */}
        {popularModels?.length > 0 && (
          <div className="mt-16 space-y-8">
            <h2 className="text-2xl font-semibold">
              Modèles les plus consultés
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {popularModels.map((model) => (
                <Card key={model.id}>
                  <div className="relative aspect-video">
                    <LazyImage
                      src={model.image}
                      alt={model.name}
                      className="object-cover rounded-t-lg"
                      blur
                    />
                  </div>
                  <CardContent className="p-6">
                    <h3 className="font-semibold mb-2">{model.name}</h3>
                    <p className="text-sm text-gray-600 mb-4">
                      {model.yearRange}
                    </p>
                    <a 
                      href={model.url}
                      className="text-blue-600 hover:underline text-sm"
                    >
                      Voir le modèle →
                    </a>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
