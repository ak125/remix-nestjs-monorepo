import { json, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData, Link } from "@remix-run/react";
import { Card, CardContent } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";
import { Pagination } from "~/components/ui/pagination";

const ITEMS_PER_PAGE = 12;

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const page = Number(url.searchParams.get("page")) || 1;
  
  const response = await fetch(
    `${process.env.API_URL}/blog/advice?page=${page}&limit=${ITEMS_PER_PAGE}`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement des articles", {
      status: response.status
    });
  }

  return json(await response.json());
}

export default function BlogAdvicePage() {
  const { articles, categories, pagination } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-7xl mx-auto">
        <div className="mb-12">
          <h1 className="text-3xl font-bold text-center">
            Conseils et Entretien Automobile
          </h1>
        </div>

        {/* Categories */}
        <div className="flex flex-wrap gap-2 mb-8">
          {categories.map((cat) => (
            <Link
              key={cat.id}
              to={`/blog/advice/category/${cat.alias}`}
              className="px-4 py-2 text-sm bg-gray-100 rounded-full hover:bg-gray-200"
            >
              {cat.name}
            </Link>
          ))}
        </div>

        {/* Articles Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          {articles.map((article) => (
            <Card key={article.id} className="flex flex-col h-full">
              <div className="relative aspect-video">
                <LazyImage
                  src={article.image}
                  alt={article.title}
                  className="object-cover rounded-t-lg"
                  blur
                />
              </div>
              <CardContent className="flex-1 p-6">
                <div className="space-y-4">
                  <div>
                    <time className="text-sm text-gray-500">
                      {formatDate(article.createdAt)}
                    </time>
                    <h2 className="font-semibold text-lg mt-1">
                      {article.title}
                    </h2>
                  </div>
                  <p className="text-gray-600 line-clamp-3">
                    {article.preview}
                  </p>
                  <Link
                    to={`/blog/advice/${article.alias}`}
                    className="text-blue-600 hover:underline inline-block"
                  >
                    Lire plus →
                  </Link>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        {/* Pagination */}
        {pagination.totalPages > 1 && (
          <Pagination 
            currentPage={pagination.currentPage}
            totalPages={pagination.totalPages}
            baseUrl="/blog/advice"
          />
        )}
      </div>
    </div>
  );
}
