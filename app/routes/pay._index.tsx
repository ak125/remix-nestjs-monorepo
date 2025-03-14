import { ActionFunctionArgs, LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useActionData, useLoaderData } from "@remix-run/react";
import { requireUserId } from "~/utils/session.server";

const PAYMENT_METHODS = [
  {
    id: "card",
    name: "Carte bancaire",
    icon: "💳",
    description: "Paiement sécurisé par carte"
  },
  {
    id: "apple_pay",
    name: "Apple Pay",
    icon: "🍎",
    description: "Payer rapidement avec Apple Pay"
  },
  {
    id: "amazon_pay",
    name: "Amazon Pay",
    icon: "📦",
    description: "Utiliser votre compte Amazon"
  }
];

export async function loader({ request }: LoaderFunctionArgs) {
  const userId = await requireUserId(request);

  const response = await fetch(
    `${process.env.API_URL}/payment/methods/${userId}`
  );

  if (!response.ok) {
    throw new Response("Erreur lors du chargement", { status: 500 });
  }

  return json(await response.json());
}

export async function action({ request }: ActionFunctionArgs) {
  const formData = await request.formData();
  const method = formData.get("method");
  const amount = formData.get("amount");

  const response = await fetch(
    `${process.env.API_URL}/payment/create`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ 
        method, 
        amount: Number(amount),
        returnUrl: `${process.env.FRONTEND_URL}/payment/success`
      })
    }
  );

  if (!response.ok) {
    return json({ error: "Erreur lors du paiement" });
  }

  const data = await response.json();
  return json(data);
}

export default function PaymentPage() {
  const { amount } = useLoaderData<typeof loader>();
  const actionData = useActionData<typeof action>();

  if (actionData?.redirectUrl) {
    window.location.href = actionData.redirectUrl;
    return null;
  }

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">Choisir un moyen de paiement</h1>

        <div className="space-y-4">
          {PAYMENT_METHODS.map(method => (
            <Form 
              key={method.id} 
              method="post" 
              className="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow"
            >
              <input type="hidden" name="method" value={method.id} />
              <input type="hidden" name="amount" value={amount} />
              
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h3 className="text-lg font-semibold flex items-center gap-2">
                    <span>{method.icon}</span>
                    {method.name}
                  </h3>
                  <p className="text-gray-600 text-sm">{method.description}</p>
                </div>
                <button
                  type="submit"
                  className="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600"
                >
                  Payer {amount}€
                </button>
              </div>
            </Form>
          ))}
        </div>

        {actionData?.error && (
          <p className="mt-4 text-red-500 text-center">
            {actionData.error}
          </p>
        )}
      </div>
    </div>
  );
}
