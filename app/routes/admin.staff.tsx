import { ActionFunctionArgs, LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useActionData, useLoaderData } from "@remix-run/react";
import { requireSuperAdmin } from "~/utils/auth.server";
import { useState } from "react";
import { Modal } from "~/components/Modal";

export async function loader({ request }: LoaderFunctionArgs) {
  const admin = await requireSuperAdmin(request);

  const response = await fetch(
    `${process.env.API_URL}/admins`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json({
    admins: await response.json(),
    currentAdmin: admin
  });
}

export async function action({ request }: ActionFunctionArgs) {
  const admin = await requireSuperAdmin(request);
  const formData = await request.formData();

  const response = await fetch(
    `${process.env.API_URL}/admins`,
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        ...Object.fromEntries(formData),
        createdById: admin.id
      })
    }
  );

  if (!response.ok) {
    const error = await response.json();
    return json({ error: error.message });
  }

  return null;
}

export default function StaffAdminPage() {
  const { admins, currentAdmin } = useLoaderData<typeof loader>();
  const [showCreateModal, setShowCreateModal] = useState(false);
  const actionData = useActionData<typeof action>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold">Gestion du Personnel</h1>

        <button
          onClick={() => setShowCreateModal(true)}
          className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
        >
          Nouveau Membre
        </button>
      </div>

      <div className="bg-white rounded-lg shadow overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Identifiant
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nom
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Contact
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Service
              </th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Statut
              </th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Actions
              </th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {admins.map(admin => (
              <tr key={admin.id} className={!admin.isActive ? 'bg-gray-50' : ''}>
                <td className="px-6 py-4">{admin.login}</td>
                <td className="px-6 py-4">
                  {admin.firstName} {admin.lastName}
                </td>
                <td className="px-6 py-4">
                  <div>{admin.email}</div>
                  <div className="text-sm text-gray-500">{admin.phone}</div>
                </td>
                <td className="px-6 py-4">
                  <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {admin.role}
                  </span>
                </td>
                <td className="px-6 py-4">
                  <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                    admin.isActive 
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  }`}>
                    {admin.isActive ? 'Actif' : 'Inactif'}
                  </span>
                </td>
                <td className="px-6 py-4 text-right">
                  {currentAdmin.id !== admin.id && (
                    <Form method="post">
                      <input type="hidden" name="adminId" value={admin.id} />
                      <button
                        type="submit"
                        name="action"
                        value="deactivate"
                        className="text-red-600 hover:text-red-900"
                      >
                        Désactiver
                      </button>
                    </Form>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {showCreateModal && (
        <Modal onClose={() => setShowCreateModal(false)}>
          <Form method="post" className="space-y-4">
            {/* Form fields */}
            {actionData?.error && (
              <div className="text-red-600">{actionData.error}</div>
            )}
          </Form>
        </Modal>
      )}
    </div>
  );
}
