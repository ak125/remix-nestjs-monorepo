import { ActionFunctionArgs, LoaderFunctionArgs, json } from "@remix-run/node";
import { Form, useActionData, useLoaderData } from "@remix-run/react";
import { useState } from "react";

const PAYMENT_METHODS = [
  {
    id: "card",
    name: "Carte bancaire",
    icon: "💳",
    description: "Paiement sécurisé par carte",
    installments: [1]
  },
  {
    id: "klarna",
    name: "Klarna",
    icon: "🔄",
    description: "Paiement en plusieurs fois sans frais",
    installments: [3, 4]
  },
  {
    id: "alma",
    name: "Alma",
    icon: "💰",
    description: "Paiement en 2, 3 ou 4 fois",
    installments: [2, 3, 4]
  },
  {
    id: "paypal",
    name: "PayPal",
    icon: "🅿️",
    description: "Paiement sécurisé via PayPal",
    installments: [1]
  }
];

export async function action({ request }: ActionFunctionArgs) {
  const formData = await request.formData();
  const method = formData.get("method");
  const installments = Number(formData.get("installments")) || 1;
  const amount = Number(formData.get("amount"));

  const response = await fetch(
    `${process.env.API_URL}/payment/create`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        method,
        installments,
        amount,
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
  const [selectedMethod, setSelectedMethod] = useState(PAYMENT_METHODS[0]);
  const [installments, setInstallments] = useState(1);

  if (actionData?.redirectUrl) {
    window.location.href = actionData.redirectUrl;
    return null;
  }

  const monthlyAmount = (amount / installments).toFixed(2);

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">Choisir un moyen de paiement</h1>

        <div className="space-y-4">
          {PAYMENT_METHODS.map(method => (
            <div 
              key={method.id}
              className={`bg-white p-6 rounded-lg shadow cursor-pointer ${
                selectedMethod.id === method.id ? 'ring-2 ring-blue-500' : ''
              }`}
              onClick={() => {
                setSelectedMethod(method);
                setInstallments(method.installments[0]);
              }}
            >
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h3 className="text-lg font-semibold flex items-center gap-2">
                    <span>{method.icon}</span>
                    {method.name}
                  </h3>
                  <p className="text-gray-600 text-sm">{method.description}</p>
                </div>
              </div>

              {selectedMethod.id === method.id && method.installments.length > 1 && (
                <div className="mt-4">
                  <select
                    value={installments}
                    onChange={e => setInstallments(Number(e.target.value))}
                    className="w-full border rounded px-3 py-2"
                  >
                    {method.installments.map(n => (
                      <option key={n} value={n}>
                        {n}x de {(amount / n).toFixed(2)}€
                      </option>
                    ))}
                  </select>
                </div>
              )}
            </div>
          ))}
        </div>

        <Form method="post" className="mt-8">
          <input type="hidden" name="method" value={selectedMethod.id} />
          <input type="hidden" name="installments" value={installments} />
          <input type="hidden" name="amount" value={amount} />

          <button
            type="submit"
            className="w-full bg-blue-500 text-white py-3 rounded-lg font-semibold hover:bg-blue-600"
          >
            {installments > 1
              ? `Payer ${amount}€ en ${installments}x de ${monthlyAmount}€`
              : `Payer ${amount}€`}
          </button>
        </Form>

        {actionData?.error && (
          <p className="mt-4 text-red-500 text-center">
            {actionData.error}
          </p>
        )}
      </div>
    </div>
  );
}
