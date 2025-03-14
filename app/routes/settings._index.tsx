import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useActionData, useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";
import { userSchema, formatZodError } from "~/utils/validation";
import { useState } from "react";
import { uploadHandler } from "~/utils/upload.server";

export async function loader({ request }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/users/${userId}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export async function action({ request }: ActionFunctionArgs) {
  const userId = await requireUserId(request);
  const formData = await request.formData();
  
  try {
    // Handle image upload if present
    const imageFile = formData.get("image") as File;
    let imageUrl = formData.get("imageUrl") as string;

    if (imageFile?.size > 0) {
      imageUrl = await uploadHandler(imageFile);
      formData.set("imageUrl", imageUrl);
    }

    // Validate form data
    const data = Object.fromEntries(formData);
    const validated = userSchema.parse(data);

    const response = await fetch(
      `${process.env.API_URL}/users/${userId}`,
      {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(validated)
      }
    );

    if (!response.ok) {
      const error = await response.json();
      return json({ error: error.message });
    }

    return redirect("/settings");
  } catch (error) {
    if (error instanceof z.ZodError) {
      return json({ errors: formatZodError(error) });
    }
    throw error;
  }
}

export default function SettingsPage() {
  const user = useLoaderData<typeof loader>();
  const actionData = useActionData<typeof action>();
  const [previewUrl, setPreviewUrl] = useState(user.imageUrl);

  const handleImageChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file) {
      const url = URL.createObjectURL(file);
      setPreviewUrl(url);
    }
  };

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">Paramètres du compte</h1>

      <div className="max-w-2xl">
        <Form method="post" encType="multipart/form-data" className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Nom
            </label>
            <input
              type="text"
              name="name"
              defaultValue={user.name || ""}
              className="w-full border rounded px-3 py-2"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              type="email"
              name="email"
              defaultValue={user.email}
              className="w-full border rounded px-3 py-2"
            />
          </div>

          <div className="space-y-2">
            <label className="block text-sm font-medium text-gray-700">
              Langue
            </label>
            <select
              name="language"
              defaultValue={user.language}
              className="w-full border rounded px-3 py-2"
            >
              <option value="fr">Français</option>
              <option value="en">English</option>
              <option value="es">Español</option>
            </select>
            {actionData?.errors?.language && (
              <p className="text-red-500 text-sm">{actionData.errors.language}</p>
            )}
          </div>

          <div className="space-y-2">
            <label className="block text-sm font-medium text-gray-700">
              Image de profil
            </label>
            <div className="flex items-center gap-4">
              {previewUrl && (
                <img
                  src={previewUrl}
                  alt="Preview"
                  className="w-20 h-20 rounded-full object-cover"
                />
              )}
              <input
                type="file"
                name="image"
                accept="image/*"
                onChange={handleImageChange}
                className="w-full border rounded px-3 py-2"
              />
            </div>
          </div>

          <div className="flex gap-4">
            <button
              type="submit"
              className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
            >
              Enregistrer
            </button>
            <button
              type="button"
              onClick={() => history.back()}
              className="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
            >
              Annuler
            </button>
          </div>
        </Form>
      </div>
    </div>
  );
}
