import { json, redirect, type LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import invariant from "tiny-invariant";

export async function loader({ params }: LoaderFunctionArgs) {
  const { pgAlias } = params;
  invariant(pgAlias, "Paramètre pgAlias requis");

  const response = await fetch(
    `${process.env.API_URL}/blog/gamme/${pgAlias}`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    // Gestion des erreurs selon le status
    switch (response.status) {
      case 410:
        throw new Response("Cette gamme n'existe plus", { status: 410 });
      case 412:
        throw new Response("Cette gamme n'est pas disponible", { status: 412 });
      default:
        throw new Response("Erreur serveur", { status: 500 });
    }
  }

  const gamme = await response.json();

  // Redirection permanente vers la nouvelle URL
  if (gamme.redirect) {
    return redirect(
      `/blog-pieces-auto/conseils/${pgAlias}`, 
      { 
        status: 301,
        headers: {
          'Cache-Control': 'public, max-age=31536000' // Cache 1 an pour les redirections 301
        }
      }
    );
  }

  return json(gamme);
}

// Cette page ne s'affiche jamais car elle redirige toujours
export default function BlogGammePage() {
  return null;
}

// Gestion des erreurs pour afficher une page 410/412 personnalisée
export function ErrorBoundary({ error }: { error: Error }) {
  return (
    <div className="container mx-auto px-4 py-8">
      <div className="max-w-xl mx-auto text-center">
        <h1 className="text-3xl font-bold mb-4">
          {error instanceof Response && error.status === 410
            ? "Cette page n'existe plus"
            : "Page non disponible"}
        </h1>
        <p className="text-gray-600 mb-6">
          {error.message}
        </p>
        <a 
          href="/blog"
          className="text-blue-600 hover:underline"
        >
          Retour au blog
        </a>
      </div>
    </div>
  );
}
