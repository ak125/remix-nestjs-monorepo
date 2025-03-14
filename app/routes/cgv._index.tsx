import { ActionFunctionArgs, LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useActionData, useLoaderData } from "@remix-run/react";
import { useState } from "react";
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
    `${process.env.API_URL}/cgv/versions`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("CGV non trouvées", { status: 404 });
  }

  const data = pageSchema.parse(await response.json());

  return json({
    versions: await response.json(),
    languages: ['fr', 'en', 'es'],
    isLoggedIn: session.has("userId")
  });
}

export async function action({ request }: ActionFunctionArgs) {
  const formData = await request.formData();
  
  const response = await fetch(
    `${process.env.API_URL}/cgv/send`,
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(Object.fromEntries(formData))
    }
  );

  if (!response.ok) {
    return json({ error: "Erreur lors de l'envoi" });
  }

  return json({ success: true });
}

export default function CGVPage() {
  const { versions, languages } = useLoaderData<typeof loader>();
  const actionData = useActionData<typeof action>();
  const [selectedVersion, setSelectedVersion] = useState(versions[0]?.version);
  const [selectedLanguage, setSelectedLanguage] = useState('fr');

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">
        Conditions Générales de Vente
      </h1>

      <div className="max-w-2xl mx-auto space-y-8">
        <div className="bg-white p-6 rounded-lg shadow">
          <h2 className="text-xl font-semibold mb-4">
            Télécharger les CGV
          </h2>
          
          <div className="grid grid-cols-2 gap-4 mb-4">
            <select
              value={selectedVersion}
              onChange={e => setSelectedVersion(e.target.value)}
              className="border rounded px-3 py-2"
            >
              {versions.map(v => (
                <option key={v.version} value={v.version}>
                  Version {v.version}
                </option>
              ))}
            </select>

            <select
              value={selectedLanguage}
              onChange={e => setSelectedLanguage(e.target.value)}
              className="border rounded px-3 py-2"
            >
              {languages.map(lang => (
                <option key={lang} value={lang}>
                  {lang.toUpperCase()}
                </option>
              ))}
            </select>
          </div>

          <a
            href={`${process.env.API_URL}/cgv/pdf?version=${selectedVersion}&language=${selectedLanguage}`}
            className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
            target="_blank"
            rel="noopener noreferrer"
          >
            Télécharger
          </a>
        </div>

        <div className="bg-white p-6 rounded-lg shadow">
          <h2 className="text-xl font-semibold mb-4">
            Recevoir par email
          </h2>

          <Form method="post" className="space-y-4">
            <input type="hidden" name="version" value={selectedVersion} />
            <input type="hidden" name="language" value={selectedLanguage} />
            
            <input
              type="email"
              name="email"
              placeholder="Votre email"
              required
              className="w-full border rounded px-3 py-2"
            />

            <button
              type="submit"
              className="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
            >
              Envoyer
            </button>

            {actionData?.success && (
              <p className="text-green-600">Email envoyé avec succès!</p>
            )}
            {actionData?.error && (
              <p className="text-red-600">{actionData.error}</p>
            )}
          </Form>
        </div>
      </div>
    </div>
  );
}
