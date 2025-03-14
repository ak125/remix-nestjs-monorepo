import { json, type ActionFunctionArgs, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData, Form, useActionData } from "@remix-run/react";
import { getSession } from "~/utils/session.server";
import { z } from "zod";
import { useState } from "react";

const pageSchema = z.object({
  title: z.string().min(3, "Le titre est trop court"),
  description: z.string().optional(),
  keywords: z.string().optional()
});

export async function loader({ request }: LoaderFunctionArgs) {
  const session = await getSession(request);

  const response = await fetch(
    `${process.env.API_URL}/page/data`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Page non trouvée", { status: 404 });
  }

  return json({
    page: await response.json(),
    isLoggedIn: session.has("userId")
  });
}

export async function action({ request }: ActionFunctionArgs) {
  const session = await getSession(request);
  if (!session.has("userId")) {
    throw new Response("Non autorisé", { status: 401 });
  }

  const formData = await request.formData();
  const validation = pageSchema.safeParse({
    title: formData.get("title"),
    description: formData.get("description"),
    keywords: formData.get("keywords")  
  });

  if (!validation.success) {
    return json({ errors: validation.error.format() }, { status: 400 });
  }

  const response = await fetch(`${process.env.API_URL}/page/save`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(validation.data)
  });

  if (!response.ok) {
    return json({ error: "Erreur lors de la sauvegarde" }, { status: 500 });
  }

  return json({ success: true });
}

export default function Page() {
  const { page, isLoggedIn } = useLoaderData<typeof loader>();
  const actionData = useActionData<typeof action>();
  const [title, setTitle] = useState(page?.title || "");
  const [description, setDescription] = useState(page?.description || "");
  const [keywords, setKeywords] = useState(page?.keywords || "");

  if (!isLoggedIn) {
    return (
      <div className="container mx-auto py-8 px-4">
        <p>Connectez-vous pour éditer la page</p>
      </div>
    );
  }

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">
          {title || "Créer une Page"}
        </h1>

        <Form method="post" className="space-y-6">
          <div>
            <label className="block mb-2">
              Titre:
              <input
                name="title"
                value={title}
                onChange={e => setTitle(e.target.value)}
                className="w-full border rounded px-3 py-2"
              />
            </label>
            {actionData?.errors?.title && (
              <p className="text-red-500 text-sm">
                {actionData.errors.title._errors[0]}
              </p>
            )}
          </div>

          <div>
            <label className="block mb-2">
              Description:
              <textarea
                name="description"
                value={description}
                onChange={e => setDescription(e.target.value)}
                className="w-full border rounded px-3 py-2"
                rows={4}
              />
            </label>
          </div>

          <div>
            <label className="block mb-2">
              Mots-clés:
              <input
                name="keywords"
                value={keywords}
                onChange={e => setKeywords(e.target.value)}
                className="w-full border rounded px-3 py-2"
              />
            </label>
          </div>

          <button 
            type="submit"
            className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
          >
            Enregistrer
          </button>
        </Form>
      </div>
    </div>
  );
}
