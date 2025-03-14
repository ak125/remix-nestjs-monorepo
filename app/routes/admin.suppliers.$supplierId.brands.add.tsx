import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";

export async function loader({ request, params }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);

  const [supplier, brands] = await Promise.all([
    fetch(`${process.env.API_URL}/suppliers/${params.supplierId}`),
    fetch(`${process.env.API_URL}/brands?unlinked=${params.supplierId}`)
  ]);

  if (!supplier.ok) {
    throw new Response("Fournisseur non trouvé", { status: 404 });
  }

  return json({
    supplier: await supplier.json(),
    brands: await brands.json()
  });
}

export async function action({ request, params }: ActionFunctionArgs) {
  const userId = await requireUserId(request);
  const formData = await request.formData();

  const response = await fetch(
    `${process.env.API_URL}/suppliers/${params.supplierId}/brands`,
    {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        brandId: formData.get('brandId'),
        userId
      })
    }
  );

  if (!response.ok) {
    const error = await response.json();
    return json({ error: error.message });
  }

  return redirect(`/admin/suppliers/${params.supplierId}`);
}

export default function AddBrandToSupplierPage() {
  const { supplier, brands } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 className="text-xl font-bold mb-6">
          Associer une Marque à {supplier.name}
        </h1>

        <Form method="post" className="space-y-6">
          <div>
            <label className="block text-sm font-medium mb-2">
              Sélectionner une marque
            </label>
            <select
              name="brandId"
              required
              className="w-full border rounded-md p-2"
            >
              <option value="">Choisir une marque...</option>
              {brands.map(brand => (
                <option key={brand.id} value={brand.id}>
                  {brand.name}
                </option>
              ))}
            </select>
          </div>

          <div className="flex justify-end gap-4">
            <a
              href={`/admin/suppliers/${supplier.id}`}
              className="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              Annuler
            </a>
            <button
              type="submit"
              className="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
            >
              Associer
            </button>
          </div>
        </Form>
      </div>
    </div>
  );
}
