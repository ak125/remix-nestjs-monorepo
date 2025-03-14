import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useLoaderData } from "@remix-run/react";
import { requireSuperAdmin } from "~/utils/auth.server";

export async function loader({ request, params }: LoaderFunctionArgs) {
  const actor = await requireSuperAdmin(request);

  const response = await fetch(
    `${process.env.API_URL}/admins/${params.adminId}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Admin non trouvé", { status: 404 });
  }

  return json({
    admin: await response.json(),
    currentAdmin: actor
  });
}

export async function action({ request, params }: ActionFunctionArgs) {
  const actor = await requireSuperAdmin(request);
  const formData = await request.formData();

  if (actor.id === params.adminId) {
    return json(
      { error: "Vous ne pouvez pas suspendre votre propre compte" },
      { status: 403 }
    );
  }

  const response = await fetch(
    `${process.env.API_URL}/admins/${params.adminId}/suspend`,
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        reason: formData.get('reason'),
        actorId: actor.id
      })
    }
  );

  if (!response.ok) {
    const error = await response.json();
    return json({ error: error.message }, { status: response.status });
  }

  return redirect('/admin/admins');
}

export default function SuspendAdminPage() {
  const { admin, currentAdmin } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 className="text-xl font-bold text-red-600 mb-6">
          Suspendre un Administrateur
        </h1>

        <div className="mb-6">
          <h2 className="font-medium mb-2">Détails de l'administrateur</h2>
          <p><span className="font-medium">Nom:</span> {admin.lastName} {admin.firstName}</p>
          <p><span className="font-medium">Email:</span> {admin.email}</p>
          <p><span className="font-medium">Rôle:</span> {admin.role}</p>
        </div>

        <Form method="post" className="space-y-4">
          <div>
            <label className="block text-sm font-medium mb-2">
              Raison de la suspension
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
              href="/admin/admins"
              className="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              Annuler
            </a>
            <button
              type="submit"
              className="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
            >
              Confirmer la suspension
            </button>
          </div>
        </Form>
      </div>
    </div>
  );
}
