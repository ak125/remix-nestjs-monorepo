import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { getSession } from "~/utils/session.server";
import { z } from "zod";

const avisSchema = z.object({
  pageTitle: z.string(),
  pageDescription: z.string(),
  pageKeywords: z.string(),
  pageContent: z.string()
});

export async function loader({ request }: LoaderFunctionArgs) {
  const session = await getSession(request);

  const response = await fetch(
    `${process.env.API_URL}/avis/data`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  const data = avisSchema.parse(await response.json());

  return json({
    ...data,
    isLoggedIn: session.has("userId")
  });
}

export default function AvisPage() {
  const { pageTitle, pageDescription, pageContent, isLoggedIn } = 
    useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold mb-6">{pageTitle}</h1>
        <p className="text-gray-600 mb-8">{pageDescription}</p>

        <div className="prose max-w-none">
          <div dangerouslySetInnerHTML={{ __html: pageContent }} />
        </div>

        {isLoggedIn && (
          <div className="mt-8 p-4 bg-blue-50 rounded">
            <p>Interface des avis réservée aux membres</p>
          </div>
        )}
      </div>
    </div>
  );
}
