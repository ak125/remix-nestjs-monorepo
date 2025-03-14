import { json, redirect, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent, CardHeader } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";
import invariant from "tiny-invariant";

export async function loader({ params, request }: LoaderFunctionArgs) {
  const { slug } = params;
  invariant(slug, "Slug requis");

  const response = await fetch(
    `${process.env.API_URL}/blog/articles/${slug}`,
    {
      headers: {
        'Cache-Control': 'public, max-age=300'
      }
    }
  );

  if (!response.ok) {
    if (response.status === 404) {
      throw new Response("Article introuvable", { status: 404 });
    }
    throw new Response("Erreur serveur", { status: 500 });
  }

  const article = await response.json();

  // Gestion de la redirection si nécessaire
  if (article.redirectUrl) {
    return redirect(article.redirectUrl, {
      status: 301,
      headers: {
        'Cache-Control': 'public, max-age=31536000'
      }
    });
  }

  return json(article);
}

export default function ArticlePage() {
  const article = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <article className="max-w-4xl mx-auto">
        <Card className="overflow-hidden">
          <CardHeader className="space-y-4">
            <div className="space-y-2">
              <time className="text-sm text-gray-500">
                {formatDate(article.createdAt)}
              </time>
              <h1 className="text-3xl font-bold tracking-tight">
                {article.title}
              </h1>
            </div>
            {article.description && (
              <p className="text-lg text-gray-600 leading-relaxed">
                {article.description}
              </p>
            )}
          </CardHeader>

          {article.coverImage && (
            <div className="relative aspect-video">
              <LazyImage
                src={article.coverImage}
                alt={article.title}
                className="object-cover"
                blur
              />
            </div>
          )}

          <CardContent className="p-6 lg:p-8">
            <div 
              className="prose prose-lg max-w-none"
              dangerouslySetInnerHTML={{ __html: article.content }}
            />
          </CardContent>
        </Card>
      </article>
    </div>
  );
}

export function ErrorBoundary({ error }: { error: Error }) {
  return (
    <div className="container mx-auto px-4 py-16">
      <div className="max-w-xl mx-auto text-center">
        <h1 className="text-3xl font-bold mb-4">
          Article non trouvé
        </h1>
        <p className="text-gray-600 mb-8">
          {error.message}
        </p>
        <a href="/blog" className="text-blue-600 hover:underline">
          Retour au blog
        </a>
      </div>
    </div>
  );
}
