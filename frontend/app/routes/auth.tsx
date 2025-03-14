import { useLoaderData } from "@remix-run/react";
import { json } from "@remix-run/node";
import { fetchAPI } from "~/utils/api";

export const loader = async () => {
  const data = await fetchAPI("/auth/verify");
  return json(data);
};

export default function AuthPage() {
  const data = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold">Statut de la session</h1>
      <p className="mt-2"><strong>Message:</strong> {data.destinationLinkMsg}</p>
      <p className="mt-2"><strong>Statut:</strong> {data.accessRequest ? "✅ Accès autorisé" : "❌ Accès refusé"}</p>
    </div>
  );
}
