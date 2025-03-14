import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card } from "~/components/ui/card";
import { LazyImage } from "~/components/ui/image";
import { formatDate } from "~/lib/utils";
import { z } from "zod";

const aliasSchema = z.object({
  pg_alias: z.string().min(3, "Alias requis")
});

export async function loader({ params }: LoaderFunctionArgs) {
  const { pg_alias } = aliasSchema.parse(params);

  const response = await fetch(
    `${process.env.API_URL}/blog-advice/article?pg_alias=${pg_alias}`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Article non trouvé", { status: 404 });
  }

  return json(await response.json());
}

export default function BlogArticle() {
  const article = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <Card className="overflow-hidden">
        <h1 className="text-3xl font-bold p-6">{article.title}</h1>
        
        <LazyImage
          src={article.image}
          alt={article.title}
          className="w-full"
          blur
        />

        <div className="p-6 space-y-4">
          <time className="text-gray-500">
            {formatDate(article.updatedAt)}
          </time>
          
          <div className="prose max-w-none">
            <p className="lead">{article.preview}</p>
            <div dangerouslySetInnerHTML={{ __html: article.content }} />
          </div>
        </div>
      </Card>
    </div>
  );
}
