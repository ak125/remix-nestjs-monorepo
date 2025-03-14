import { json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";

export async function loader() {
  const response = await fetch(
    `${process.env.API_URL}/blog-advice/most-read`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export default function BlogMostRead() {
  const articles = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <h2 className="text-2xl font-bold mb-8">Articles les plus lus</h2>
      
      <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {articles.map((article) => (
          <Card key={article.id} className="overflow-hidden">
            <div className="relative aspect-video">
              <LazyImage
                src={article.image}
                alt={article.title}
                className="object-cover"
                blur
              />
            </div>

            <CardContent className="p-4">
              <h3 className="font-semibold mb-2">
                {article.title}
              </h3>
              <time className="text-sm text-gray-500">
                {formatDate(article.updatedAt)}
              </time>
              <a 
                href={`/blog-advice/${article.alias}`}
                className="text-blue-600 hover:underline text-sm block mt-2"
              >
                Lire l'article →
              </a>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
