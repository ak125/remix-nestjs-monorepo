import { json, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";

export async function loader({ request }: LoaderFunctionArgs) {
  const response = await fetch(
    `${process.env.API_URL}/blog/entretien`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement des articles", {
      status: response.status
    });
  }

  return json(await response.json());
}

export default function EntretienBlogPage() {
  const { categories } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <div className="mb-12 text-center">
          <h1 className="text-3xl font-bold">
            Conseils Entretien Automobile
          </h1>
          <p className="text-gray-600 mt-2">
            Découvrez nos guides pratiques pour l'entretien de votre véhicule
          </p>
        </div>

        {/* Categories */}
        <div className="space-y-16">
          {categories.map((category) => (
            <section key={category.id} className="space-y-8">
              <h2 className="text-2xl font-semibold">
                {category.name}
              </h2>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {category.articles.map((article) => (
                  <Card key={article.id} className="flex h-full">
                    <div className="relative aspect-[4/3] w-1/3 shrink-0">
                      <LazyImage
                        src={article.image}
                        alt={article.title}
                        className="object-cover rounded-l-lg"
                        blur
                      />
                    </div>

                    <CardContent className="flex-1 p-4">
                      <div className="space-y-2">
                        <div>
                          <time className="text-sm text-gray-500">
                            {formatDate(article.updatedAt)}
                          </time>
                          <h3 className="font-semibold mt-1 line-clamp-2">
                            {article.title}
                          </h3>
                        </div>

                        <p className="text-sm text-gray-600 line-clamp-2">
                          {article.preview}
                        </p>

                        <a
                          href={`/blog/entretien/${article.alias}`}
                          className="text-blue-600 hover:underline text-sm inline-block"
                        >
                          Lire l'article →
                        </a>
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            </section>
          ))}
        </div>
      </div>
    </div>
  );
}
