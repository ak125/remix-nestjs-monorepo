import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { Editor } from "~/components/Editor";
import { useState } from "react";

export async function loader({ request }: LoaderFunctionArgs) {
  await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/ml`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export async function action({ request }: ActionFunctionArgs) {
  await requireUserId(request);
  const formData = await request.formData();

  const response = await fetch(
    `${process.env.API_URL}/ml`,
    {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(Object.fromEntries(formData))
    }
  );

  if (!response.ok) {
    return json({ error: "Erreur lors de la mise à jour" });
  }

  return redirect("/ml");
}

export default function EditMLPage() {
  const page = useLoaderData<typeof loader>();
  const [content, setContent] = useState(page.content);

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">
        Modifier la page Machine Learning
      </h1>

      <Form method="post" className="space-y-6">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Titre
          </label>
          <input
            type="text"
            name="title"
            defaultValue={page.title}
            className="w-full border rounded px-3 py-2"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Description
          </label>
          <textarea
            name="description"
            defaultValue={page.description}
            rows={3}
            className="w-full border rounded px-3 py-2"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Contenu
          </label>
          <Editor
            value={content}
            onChange={setContent}
            name="content"
          />
        </div>

        <div className="flex gap-4">
          <button
            type="submit"
            className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
          >
            Enregistrer
          </button>
          <a
            href="/ml"
            className="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
          >
            Annuler
          </a>
        </div>
      </Form>
    </div>
  );
}
