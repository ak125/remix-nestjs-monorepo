import { useLoaderData } from "@remix-run/react";
import { json, type LoaderFunctionArgs } from "@remix-run/node";
import { Card, CardContent, CardFooter } from "~/components/ui/card";
import { Button } from "~/components/ui/button";
import { formatDate } from "~/lib/utils";
import { LazyImage } from "~/components/ui/image";

const ARTICLES_PER_PAGE = 6;

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const page = Number(url.searchParams.get("page")) || 1;

  const [articlesRes, totalRes] = await Promise.all([
    // Get paginated articles
    fetch(`${process.env.API_URL}/blog/articles/recent?page=${page}&limit=${ARTICLES_PER_PAGE}`),
    // Get total count
    fetch(`${process.env.API_URL}/blog/articles/count`)
  ]);

  const [articles, { total }] = await Promise.all([
    articlesRes.json(),
    totalRes.json()
  ]);

  return json({
    articles,
    pagination: {
      page,
      total,
      totalPages: Math.ceil(total / ARTICLES_PER_PAGE)
    }
  }, {
    headers: {
      'Cache-Control': 'public, max-age=300'
    }
  });
}

export default function Blog() {
  const { articles, pagination } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-7xl mx-auto">
        <h1 className="text-3xl font-bold text-center mb-12">
          Blog Automecanik
        </h1>

        {/* Articles Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          {articles.map((article) => (
            <Card key={article.id} className="flex flex-col h-full">
              <div className="relative aspect-video">
                <LazyImage
                  src={article.image}
                  alt={article.title}
                  className="object-cover w-full h-full rounded-t-lg"
                  blur
                />
              </div>
              <CardContent className="flex-1 p-6">
                <div className="text-sm text-gray-500 mb-2">
                  {formatDate(article.updatedAt)}
                </div>
                <h3 className="font-semibold text-lg mb-2 line-clamp-2">
                  {article.title}
                </h3>
                <p className="text-gray-600 line-clamp-3">
                  {article.preview}
                </p>
              </CardContent>
              <CardFooter className="p-6 pt-0">
                <Button asChild variant="outline" className="w-full">
                  <a href={`/blog/article/${article.alias}`}>
                    Lire plus
                  </a>
                </Button>
              </CardFooter>
            </Card>
          ))}
        </div>

        {/* Pagination */}
        {pagination.totalPages > 1 && (
          <div className="flex justify-center items-center gap-4">
            {pagination.page > 1 && (
              <Button variant="outline" asChild>
                <a href={`?page=${pagination.page - 1}`}>
                  ← Précédent
                </a>
              </Button>
            )}
            
            <span className="text-sm text-gray-500">
              Page {pagination.page} sur {pagination.totalPages}
            </span>

            {pagination.page < pagination.totalPages && (
              <Button variant="outline" asChild>
                <a href={`?page=${pagination.page + 1}`}>
                  Suivant →
                </a>
              </Button>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
