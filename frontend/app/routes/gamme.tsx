import { useLoaderData, redirect } from "@remix-run/react";
import { json, LoaderFunction } from "@remix-run/node";
import { fetchAPI } from "~/utils/api";
import GammeInfo from "~/components/GammeInfo";

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const pgId = url.searchParams.get("pg_id");

  if (!pgId) {
    throw new Response("Gamme non trouvée", { status: 404 });
  }

  // Redirections spécifiques
  if (pgId === "3940") {
    return redirect("/pieces/corps-papillon-158.html", 301);
  }

  try {
    const data = await fetchAPI(`/gamme/details?pg_id=${pgId}`);

    if (!data.display) {
      return redirect("/erreur/412", { status: 412 });
    }

    return json(data);
  } catch (error) {
    return redirect("/erreur/410", { status: 410 }); 
  }
};

export default function GammePage() {
  const gamme = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold">{gamme.title}</h1>
      <GammeInfo gamme={gamme} />
    </div>
  );
}
