import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";

export async function loader({ request, params }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/pieces/${params.pieceId}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Pièce non trouvée", { status: 404 });
  }

  return json(await response.json());
}

export async function action({ request, params }: ActionFunctionArgs) {
  const userId = await requireUserId(request);
  const formData = await request.formData();

  const response = await fetch(
    `${process.env.API_URL}/pieces/${params.pieceId}/deactivate`,
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        reason: formData.get('reason'),
        userId
      })
    }
  );

  if (!response.ok) {
    return json({ error: "Erreur lors de la désactivation" });
  }

  return redirect('/admin/pieces');
}

export default function DeactivatePiecePage() {
  const piece = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 className="text-xl font-bold mb-6">
          Désactiver la pièce
        </h1>

        <div className="mb-6">
          <h2 className="font-medium mb-2">Détails de la pièce</h2>
          <p><span className="font-medium">Référence:</span> {piece.reference}</p>
          <p><span className="font-medium">Nom:</span> {piece.name}</p>
          <p><span className="font-medium">Marque:</span> {piece.brand.name}</p>
        </div>

        <Form method="post" className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-2">
              Raison de la désactivation
            </label>
            <textarea
              name="reason"
              required
              className="w-full border rounded-md p-2 h-24"
              placeholder="Veuillez indiquer la raison..."
            />
          </div>

          <div className="flex justify-end gap-4">
            <a 
              href="/admin/pieces"
              className="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              Annuler
            </a>
            <button
              type="submit"
              className="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
            >
              Confirmer la désactivation
            </button>
          </div>
        </Form>
      </div>
    </div>
  );
}
