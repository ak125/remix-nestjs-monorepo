import { json } from "@remix-run/node";
import { cache } from "~/lib/cache.server";

export async function loader({ request }) {
  const url = new URL(request.url);
  const gammeId = url.searchParams.get("gammeId");
  const marqueId = url.searchParams.get("marqueId");

  // Si des paramètres sont manquants, retourner une erreur
  if (!gammeId || !marqueId) {
    return json({ error: "Les paramètres gammeId et marqueId sont requis" }, { status: 400 });
  }

  // Clé pour la mise en cache
  const cacheKey = `car-years:${gammeId}:${marqueId}`;

  // Vérifier si les données sont en cache
  const cachedData = await cache.get(cacheKey);
  if (cachedData) {
    return json(cachedData);
  }

  try {
    // Récupérer les années depuis l'API NestJS
    const apiUrl = `${process.env.API_BASE_URL || 'http://localhost:3000'}/api/models/years`;
    const response = await fetch(
      `${apiUrl}?gammeId=${gammeId}&marqueId=${marqueId}`,
      {
        headers: {
          "Content-Type": "application/json"
        }
      }
    );

    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }

    const data = await response.json();

    // Mettre en cache pour 30 minutes
    await cache.set(cacheKey, data, 30 * 60);

    return json(data);
  } catch (error) {
    console.error("Error fetching car years:", error);
    return json({ error: "Une erreur est survenue lors de la récupération des années" }, { status: 500 });
  }
}
