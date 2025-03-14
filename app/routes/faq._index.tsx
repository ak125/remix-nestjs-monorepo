import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { getSession } from "~/utils/session.server";
import { formatDate } from "~/lib/utils";
import { z } from "zod";

const faqSchema = z.array(z.object({
  id: z.number(),
  question: z.string(),
  answer: z.string(),
  createdAt: z.string().datetime()
}));

export async function loader({ request }: LoaderFunctionArgs) {
  const session = await getSession(request);

  const response = await fetch(
    `${process.env.API_URL}/faq/data`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("FAQ non trouvée", { status: 404 });
  }

  const faqs = faqSchema.parse(await response.json());

  return json({
    faqs,
    isLoggedIn: session.has("userId")
  });
}

export default function FAQPage() {
  const { faqs } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">
          Foire aux Questions (FAQ)
        </h1>

        {faqs.length === 0 ? (
          <p>Aucune question disponible.</p>
        ) : (
          <div className="space-y-6">
            {faqs.map((faq) => (
              <div key={faq.id} className="bg-white p-6 rounded-lg shadow">
                <h3 className="font-semibold text-lg mb-2">
                  {faq.question}
                </h3>
                <p className="text-gray-600">{faq.answer}</p>
                <p className="text-sm text-gray-500 mt-2">
                  Mise à jour le {formatDate(faq.createdAt)}
                </p>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
