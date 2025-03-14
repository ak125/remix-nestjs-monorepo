import { json, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import invariant from "tiny-invariant";

export async function loader({ params }: LoaderFunctionArgs) {
  const { marque_alias, mdg_alias } = params;
  invariant(marque_alias, "Marque requise");
  invariant(mdg_alias, "Modèle requis");

  const response = await fetch(
    `${process.env.API_URL}/constructeurs/${marque_alias}/${mdg_alias}`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Modèle non trouvé", { status: 404 });
  }

  return json(await response.json());
}

export default function ConstructeurModelPage() {
  const { modele, marque, motorisations, pieces } = useLoaderData<typeof loader>();

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
                {marque.name} {modele.name}
              </h1>
              <p className="text-gray-600 mt-2">
                Trouvez toutes les pièces détachées pour votre véhicule
              </p>
            </div>
          </div>
          
          {modele.image && (
            <div className="relative aspect-video">
              <LazyImage
                src={modele.image}
                alt={`${marque.name} ${modele.name}`}
                className="object-cover rounded-lg"
                blur
              />
            </div>
          )}
        </div>

        {/* Motorisations */}
        <div className="space-y-8 mb-16">
          <h2 className="text-2xl font-semibold">Motorisations disponibles</h2>
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {motorisations.map((moteur) => (
              <Card key={moteur.id} className="hover:shadow-lg transition">
                <CardContent className="p-6">
                  <div className="flex justify-between items-start">
                    <div>
                      <h3 className="font-semibold">{moteur.name}</h3>
                      <div className="text-sm text-gray-600 space-y-1 mt-2">
                        <p>Puissance: {moteur.powerPs} ch</p>
                        <p>Carburant: {moteur.fuel}</p>
                        <p>Période: {moteur.yearRange}</p>
                      </div>
                    </div>
                    <a
                      href={moteur.url}
                      className="text-blue-600 hover:underline text-sm"
                    >
                      Voir les pièces →
                    </a>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>

        {/* Pièces populaires */}
        {pieces?.length > 0 && (
          <div className="space-y-8">
            <h2 className="text-2xl font-semibold">
              Pièces les plus recherchées
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {pieces.map((piece) => (
                <Card key={piece.id} className="hover:shadow-lg transition">
                  <div className="relative aspect-video">
                    <LazyImage
                      src={piece.image}
                      alt={piece.name}
                      className="object-contain p-4"
                      blur
                    />
                  </div>
                  <CardContent className="p-6">
                    <h3 className="font-semibold mb-2">{piece.name}</h3>
                    <p className="text-sm text-gray-600 mb-4">
                      {piece.description}
                    </p>
                    <a
                      href={piece.url}
                      className="text-blue-600 hover:underline text-sm"
                    >
                      Voir le détail →
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
