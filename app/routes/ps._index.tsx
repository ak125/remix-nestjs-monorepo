import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { getSession } from "~/utils/session.server";
import { z } from "zod";

const pageSchema = z.object({
  title: z.string(),
  description: z.string(),
  keywords: z.string(),
  content: z.string()
});

export async function loader({ request }: LoaderFunctionArgs) {
  const session = await getSession(request);

  const response = await fetch(
    `${process.env.API_URL}/ps/data`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Page non trouvée", { status: 404 });
  }

  const data = pageSchema.parse(await response.json());

  return json({
    ...data,
    isLoggedIn: session.has("userId")
  });
}

export default function PsPage() {
  const { title, description, content, isLoggedIn } = 
    useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold mb-6">{title}</h1>
        <p className="text-gray-600 mb-8">{description}</p>

        <div className="prose max-w-none">
          <div dangerouslySetInnerHTML={{ __html: content }} />
        </div>

        {isLoggedIn && (
          <div className="mt-8 p-4 bg-blue-50 rounded">
            <p>Zone réservée aux membres</p>
          </div>
        )}
      </div>
    </div>
  );
}
