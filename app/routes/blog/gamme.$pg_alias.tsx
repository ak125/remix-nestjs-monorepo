import { json, redirect, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";
import invariant from "tiny-invariant";

export async function loader({ params }: LoaderFunctionArgs) {
  const { pg_alias } = params;
  invariant(pg_alias, "Alias de gamme requis");

  // Appel à l'API
  const response = await fetch(
    `${process.env.API_URL}/blog/gammes/${pg_alias}`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  // Gestion des erreurs
  if (!response.ok) {
    switch (response.status) {
      case 410:
        throw new Response("Cette gamme n'existe plus", { status: 410 });
      case 404:
        throw new Response("Gamme introuvable", { status: 404 });
      default:
        throw new Response("Erreur serveur", { status: 500 });
    }
  }

  const data = await response.json();

  // Redirection spécifique
  if (data.redirect) {
    return redirect(data.redirect, {
      status: 301,
      headers: { 'Cache-Control': 'public, max-age=31536000' }
    });
  }

  return json(data);
}

export default function GammePage() {
  const { article, gamme } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-4xl mx-auto">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold mb-2">{article.title}</h1>
          <div className="flex gap-4 text-sm text-gray-500">
            <time>
              Publié le {formatDate(article.createdAt)}
            </time>
            <time>
              Modifié le {formatDate(article.updatedAt)}
            </time>
          </div>
        </div>

        {/* Content */}
        <Card className="overflow-hidden">
          {article.image && (
            <div className="relative aspect-video">
              <LazyImage
                src={article.image}
                alt={article.title}
                className="object-cover"
                blur
              />
            </div>
          )}

          <CardContent className="prose max-w-none p-6 lg:p-8">
            <p className="lead">{article.preview}</p>
            <div dangerouslySetInnerHTML={{ __html: article.content }} />
          </CardContent>
        </Card>

        {/* Related Articles */}
        {article.related?.length > 0 && (
          <div className="mt-12 space-y-8">
            <h2 className="text-2xl font-semibold">
              Articles liés à {gamme.name}
            </h2>
            <div className="grid sm:grid-cols-2 gap-6">
              {article.related.map((related) => (
                <Card key={related.id} className="hover:shadow-lg transition">
                  <div className="relative aspect-video">
                    <LazyImage
                      src={related.image}
                      alt={related.title}
                      className="object-cover rounded-t-lg"
                      blur
                    />
                  </div>
                  <CardContent className="p-6">
                    <h3 className="font-semibold mb-2">
                      {related.title}
                    </h3>
                    <p className="text-sm text-gray-600 mb-4">
                      {related.preview}
                    </p>
                    <a 
                      href={`/blog/gamme/${related.alias}`}
                      className="text-blue-600 hover:underline text-sm"
                    >
                      Lire l'article →
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
