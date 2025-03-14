import { json, LoaderFunction, ActionFunction } from "@remix-run/node";

/**
 * Cette page agit comme un honeypot pour détecter les bots malveillants.
 * Elle n'est liée nulle part sur le site et n'est pas indexable,
 * donc seuls les bots qui scrapent toutes les URLs vont la visiter.
 */
export const loader: LoaderFunction = async ({ request }) => {
  const userAgent = request.headers.get("User-Agent") || "unknown";
  const ip = request.headers.get("X-Forwarded-For") || 
             request.headers.get("X-Real-IP") || 
             "unknown";
  
  try {
    // Signaler le bot suspect à l'API
    const apiUrl = `${process.env.API_BASE_URL || 'http://localhost:3000'}/api/robots/report`;
    await fetch(apiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        userAgent,
        ipAddress: ip,
        path: "/hidden-link"
      })
    });
  } catch (error) {
    console.error("Erreur lors du signalement d'un bot suspect:", error);
  }
  
  // Retourner une erreur 404 pour ne pas éveiller les soupçons
  return new Response("Not found", {
    status: 404,
    headers: {
      "Content-Type": "text/html",
      "X-Robots-Tag": "noindex, nofollow"
    }
  });
};

export default function HiddenLink() {
  return null;
}
