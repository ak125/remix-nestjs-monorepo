import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useActionData, useLoaderData } from "@remix-run/react";
import { getSession } from "~/utils/session.server";
import { z } from "zod";

const devisSchema = z.object({
  name: z.string().min(2, "Nom trop court"),
  email: z.string().email("Email invalide"),
  details: z.string().min(10, "Description trop courte")
});

export async function loader({ request }: LoaderFunctionArgs) {
  const session = await getSession(request);

  const response = await fetch(`${process.env.API_URL}/devis/data`);
  if (!response.ok) {
    throw new Response("Page non trouvée", { status: 404 });
  }

  const data = await response.json();
  return json({ ...data, isLoggedIn: session.has("userId") });
}

export async function action({ request }: ActionFunctionArgs) {
  const formData = Object.fromEntries(await request.formData());

  try {
    const validated = devisSchema.parse(formData);
    
    const response = await fetch(`${process.env.API_URL}/devis/submit`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(validated)
    });

    if (!response.ok) throw new Error();

    return redirect("/devis/success");
  } catch (error) {
    if (error instanceof z.ZodError) {
      return json({ errors: error.format() });
    }
    return json({ error: "Erreur lors de l'envoi" });
  }
}

export default function DevisPage() {
  const { title, description, content } = useLoaderData<typeof loader>();
  const actionData = useActionData<typeof action>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold mb-6">{title}</h1>
        <p className="text-gray-600 mb-8">{description}</p>

        <Form method="post" className="space-y-6">
          {/* Champs du formulaire */}
          <div className="space-y-4">
            <div>
              <label className="block mb-2">Nom :</label>
              <input
                type="text"
                name="name"
                className="w-full border rounded px-3 py-2"
                required
              />
              {actionData?.errors?.name && (
                <p className="text-red-500 text-sm mt-1">
                  {actionData.errors.name._errors[0]}
                </p>
              )}
            </div>

            <div>
              <label className="block mb-2">Email :</label>
              <input
                type="email"
                name="email"
                className="w-full border rounded px-3 py-2"
                required
              />
              {actionData?.errors?.email && (
                <p className="text-red-500 text-sm mt-1">
                  {actionData.errors.email._errors[0]}
                </p>
              )}
            </div>

            <div>
              <label className="block mb-2">Détails :</label>
              <textarea
                name="details"
                rows={6}
                className="w-full border rounded px-3 py-2"
                required
              />
              {actionData?.errors?.details && (
                <p className="text-red-500 text-sm mt-1">
                  {actionData.errors.details._errors[0]}
                </p>
              )}
            </div>
          </div>

          <button
            type="submit"
            className="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600"
          >
            Envoyer la demande
          </button>
        </Form>
      </div>
    </div>
  );
}
