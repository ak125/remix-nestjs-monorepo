import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useActionData, useLoaderData } from "@remix-run/react";
import { getSession } from "~/utils/session.server";

export async function loader({ request }: LoaderFunctionArgs) {
  const session = await getSession(request);
  const url = new URL(request.url);
  
  const response = await fetch(
    `${process.env.API_URL}/cgv/versions?latest=true`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Version non trouvée", { status: 404 });
  }

  return json({
    version: await response.json(),
    userEmail: session.get("userEmail")
  });
}

export async function action({ request }: ActionFunctionArgs) {
  const formData = await request.formData();

  const response = await fetch(
    `${process.env.API_URL}/cgv/sign`,
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(Object.fromEntries(formData))
    }
  );

  if (!response.ok) {
    return json({ error: "Erreur lors de la signature" });
  }

  return redirect("/cgv/thankyou");
}

export default function SignCGVPage() {
  const { version, userEmail } = useLoaderData<typeof loader>();
  const actionData = useActionData<typeof action>();

  return (
    <div className="container mx-auto py-8 px-4 max-w-xl">
      <h1 className="text-3xl font-bold mb-8">
        Signer les CGV
      </h1>

      <div className="bg-white rounded-lg shadow p-6">
        <Form method="post" className="space-y-6">
          <input type="hidden" name="version" value={version.number} />
          
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              type="email"
              name="email"
              defaultValue={userEmail}
              required
              className="w-full border rounded px-3 py-2"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Nom complet
            </label>
            <input
              type="text"
              name="name"
              required
              className="w-full border rounded px-3 py-2"
            />
          </div>

          <div className="flex items-center">
            <input
              type="checkbox"
              required
              className="mr-2"
            />
            <span className="text-sm text-gray-600">
              J'ai lu et j'accepte les CGV version {version.number}
            </span>
          </div>

          {actionData?.error && (
            <p className="text-red-600 text-sm">{actionData.error}</p>
          )}

          <button
            type="submit"
            className="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
          >
            Signer électroniquement
          </button>
        </Form>
      </div>
    </div>
  );
}
