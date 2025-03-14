import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { getSession } from "~/utils/session.server";
import { formatDate } from "~/lib/utils";
import { z } from "zod";

const pageSchema = z.object({
  title: z.string(),
  description: z.string(),
  content: z.string(),
  lastUpdated: z.string().datetime().optional()
});

export async function loader({ request }: LoaderFunctionArgs) {
  const session = await getSession(request);

  const response = await fetch(
    `${process.env.API_URL}/recommendations/data`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Recommandations non trouvées", { status: 404 });
  }

  const data = pageSchema.parse(await response.json());

  return json({
    ...data,
    isLoggedIn: session.has("userId")
  });
}

export default function RecommendationsPage() {
  const { title, description, content, lastUpdated } = 
    useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold mb-6">{title}</h1>
        <p className="text-gray-600 mb-8">{description}</p>

        {lastUpdated && (
          <p className="text-sm text-gray-500 mb-8">
            Dernière mise à jour : {formatDate(lastUpdated)}
          </p>
        )}

        <div className="prose max-w-none">
          <div 
            className="space-y-4"
            dangerouslySetInnerHTML={{ __html: content }} 
          />
        </div>
      </div>
    </div>
  );
}
