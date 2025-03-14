import { json, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";

export async function loader({ request }: LoaderFunctionArgs) {
  const response = await fetch(
    `${process.env.API_URL}/blog/constructeurs`,
    {
      headers: {
        'Cache-Control': 'public, max-age=300'
      }
    }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement des constructeurs", {
      status: response.status
    });
  }

  return json(await response.json());
}

export default function ConstructeursPage() {
  const { marques, modelesPopulaires } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-7xl mx-auto">
        <h1 className="text-3xl font-bold text-center mb-12">
          Marques des constructeurs automobile
        </h1>

        {/* Grille des marques */}
        <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4 mb-16">
          {marques.map((marque) => (
            <a
              key={marque.id}
              href={`/blog/constructeurs/${marque.alias}`}
              className="group p-4 bg-white rounded-lg hover:shadow-lg transition"
            >
              <LazyImage
                src={marque.logo}
                alt={marque.name}
                className="w-full aspect-square object-contain"
                blur
              />
              <p className="text-sm text-center mt-2 text-gray-600 group-hover:text-blue-600">
                {marque.name}
              </p>
            </a>
          ))}
        </div>

        {/* Section d'information */}
        <div className="mb-16">
          <Card className="p-6">
            <h2 className="text-2xl font-semibold mb-4">
              Les pièces autos d'origine OEM
            </h2>
            <div className="prose max-w-none text-gray-600">
              <p>
                OEM (Original Equipment Manufacturer) désigne les pièces fournies par 
                les fabricants sous contrat avec les constructeurs automobiles. 
                Ces pièces sont identiques à celles montées en usine, avec le logo 
                du constructeur et sa garantie.
              </p>
              {/* ...autres paragraphes d'information... */}
            </div>
          </Card>
        </div>

        {/* Modèles populaires */}
        {modelesPopulaires?.length > 0 && (
          <div className="space-y-8">
            <h2 className="text-2xl font-semibold">
              Les modèles les plus consultés
            </h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {modelesPopulaires.map((modele) => (
                <Card key={modele.id} className="hover:shadow-lg transition">
                  <div className="relative aspect-video">
                    <LazyImage
                      src={modele.image}
                      alt={modele.name}
                      className="object-cover rounded-t-lg"
                      blur
                    />
                  </div>
                  <CardContent className="p-6">
                    <h3 className="font-semibold mb-2">
                      {modele.marque.name} {modele.name}
                    </h3>
                    <p className="text-sm text-gray-600 mb-4">
                      {modele.motorisation}
                    </p>
                    <a 
                      href={`/blog/constructeurs/${modele.marque.alias}/${modele.alias}`}
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
