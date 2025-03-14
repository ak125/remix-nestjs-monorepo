import { json, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";

export async function loader({ request }: LoaderFunctionArgs) {
  const response = await fetch(
    `${process.env.API_URL}/blog/guides`,
    { 
      headers: { 'Cache-Control': 'public, max-age=300' }
    }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement des guides", {
      status: response.status
    });
  }

  return json(await response.json());
}

export default function GuidePage() {
  const { guides } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-7xl mx-auto">
        <h1 className="text-3xl font-bold text-center mb-12">
          Guides & Conseils Auto
        </h1>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {guides.map((guide) => (
            <Card key={guide.id} className="flex flex-col h-full">
              <div className="relative aspect-video">
                <LazyImage
                  src={guide.image}
                  alt={guide.title}
                  className="object-cover rounded-t-lg"
                  blur
                />
              </div>
              <CardContent className="flex-1 p-6">
                <div className="space-y-4">
                  <div>
                    <time className="text-sm text-gray-500">
                      {formatDate(guide.updatedAt)}
                    </time>
                    <h2 className="font-semibold text-lg mt-1">
                      {guide.title}
                    </h2>
                  </div>
                  <p className="text-gray-600 line-clamp-3">
                    {guide.preview}
                  </p>
                  <a 
                    href={`/blog/guide/${guide.alias}`}
                    className="text-blue-600 hover:underline inline-block"
                  >
                    Lire plus →
                  </a>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* SEO Content */}
        <div className="mt-16">
          <Card className="p-6">
            <div className="prose max-w-none">
              <h2 className="text-2xl font-semibold mb-4">
                Tout savoir sur l'entretien automobile
              </h2>
              <p className="text-gray-600">
                Découvrez nos guides pratiques pour l'entretien de votre véhicule. 
                Des conseils d'experts pour maintenir votre voiture en parfait état 
                et optimiser sa longévité.
              </p>
            </div>
          </Card>
        </div>
      </div>
    </div>
  );
}
