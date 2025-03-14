import { ActionFunctionArgs, LoaderFunctionArgs, json, redirect } from "@remix-run/node";
import { Form, useActionData, useLoaderData } from "@remix-run/react";
import { getSession } from "~/utils/session.server";
import { z } from "zod";

const contactSchema = z.object({
  name: z.string().min(2, "Nom trop court"),
  email: z.string().email("Email invalide"),
  message: z.string().min(10, "Message trop court")
});

export async function loader({ request }: LoaderFunctionArgs) {
  const session = await getSession(request);

  const response = await fetch(
    `${process.env.API_URL}/contact/data`, 
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  const data = contactSchema.parse(await response.json());

  return json({
    ...data,
    isLoggedIn: session.has("userId")
  });
}

export async function action({ request }: ActionFunctionArgs) {
  const formData = Object.fromEntries(await request.formData());
  
  try {
    const validatedData = contactSchema.parse(formData);
    
    const response = await fetch(`${process.env.API_URL}/contact/submit`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(validatedData)
    });

    if (!response.ok) {
      throw new Error("Erreur lors de l'envoi");
    }

    return redirect("/contact/success");
  } catch (error) {
    if (error instanceof z.ZodError) {
      return json({ errors: error.format() }, { status: 400 });
    }
    return json({ error: "Erreur serveur" }, { status: 500 });
  }
}

export default function ContactPage() {
  const actionData = useActionData<typeof action>();
  const { pageTitle, pageDescription } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold mb-6">{pageTitle}</h1>
        <p className="text-gray-600 mb-8">{pageDescription}</p>

        <Form method="post" className="space-y-6">
          <div>
            <label className="block mb-2">
              Nom :
              <input
                type="text"
                name="name"
                className="w-full border rounded px-3 py-2"
                required
              />
            </label>
            {actionData?.errors?.name && (
              <p className="text-red-500 text-sm">
                {actionData.errors.name._errors[0]}
              </p>
            )}
          </div>

          <div>
            <label className="block mb-2">
              Email :
              <input
                type="email"
                name="email"
                className="w-full border rounded px-3 py-2"
                required
              />
            </label>
            {actionData?.errors?.email && (
              <p className="text-red-500 text-sm">
                {actionData.errors.email._errors[0]}
              </p>
            )}
          </div>

          <div>
            <label className="block mb-2">
              Message :
              <textarea
                name="message"
                className="w-full border rounded px-3 py-2"
                rows={6}
                required
              />
            </label>
            {actionData?.errors?.message && (
              <p className="text-red-500 text-sm">
                {actionData.errors.message._errors[0]}
              </p>
            )}
          </div>

          <button
            type="submit"
            className="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600"
          >
            Envoyer
          </button>
        </Form>
      </div>
    </div>
  );
}
