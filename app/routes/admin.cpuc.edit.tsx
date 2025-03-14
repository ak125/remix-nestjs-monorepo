import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { Editor } from "~/components/Editor";
import { useState } from "react";

export async function loader({ request }: LoaderFunctionArgs) {
  await requireUserId(request);
  
  const language = new URL(request.url).searchParams.get("lang") || "fr";
  
  const response = await fetch(
    `${process.env.API_URL}/cpuc?language=${language}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("CPUC non trouvées", { status: 404 });
  }

  return json(await response.json());
}

export async function action({ request }: ActionFunctionArgs) {
  const userId = await requireUserId(request);
  const formData = await request.formData();

  const response = await fetch(
    `${process.env.API_URL}/cpuc`,
    {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(Object.fromEntries(formData))
    }
  );

  if (!response.ok) {
    return json({ error: "Erreur lors de la mise à jour" });
  }

  return redirect("/admin/cpuc");
}

export default function EditCPUCPage() {
  const cpuc = useLoaderData<typeof loader>();
  const [content, setContent] = useState(cpuc.content);

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold">
          Modifier les CGU
        </h1>
        <a
          href={`${process.env.API_URL}/cpuc/pdf`}
          className="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
          target="_blank"
          rel="noopener noreferrer"
        >
          Télécharger PDF
        </a>
      </div>

      <Form method="post" className="space-y-6">
        <div>
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
            href="/admin/cpuc"
            className="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
          >
            Annuler
          </a>
        </div>
      </Form>
    </div>
  );
}
