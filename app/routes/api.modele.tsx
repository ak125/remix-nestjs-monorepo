import { json } from "@remix-run/node";
import { cache } from "~/lib/cache.server";

export async function loader({ request }) {
  const url = new URL(request.url);
  const path = url.pathname.split('/').pop();
  const params = Object.fromEntries(url.searchParams);
  
  // Récupérer les paramètres communs
  const gammeId = params.gammeId;
  const marqueId = params.marqueId;
  
  // Valider les paramètres obligatoires
  if (!gammeId || !marqueId) {
    return json({ error: "Les paramètres gammeId et marqueId sont requis" }, { status: 400 });
  }
  
  // Déterminer l'endpoint API à appeler
  const apiEndpoint = determineApiEndpoint(path, params);
  
  // Clé de cache basée sur l'URL complète
  const cacheKey = `api:${url.pathname}${url.search}`;
  
  try {
    // Vérifier si les données sont en cache
    const cachedData = await cache.get(cacheKey);
    if (cachedData) {
      return json(cachedData);
    }
    
    // Appeler l'API NestJS backend
    const apiBaseUrl = process.env.API_BASE_URL || 'http://localhost:3000';
    const response = await fetch(`${apiBaseUrl}${apiEndpoint}`);
    
    if (!response.ok) {
      console.error(`API error: ${response.status} ${response.statusText}`);
      return json({ error: "Erreur lors de la récupération des données" }, { status: response.status });
    }
    
    const data = await response.json();
    
    // Mettre en cache pendant 5 minutes (300 secondes)
    await cache.set(cacheKey, data, 300);
    
    return json(data);
  } catch (error) {
    console.error("Error fetching from API:", error);
    return json({ error: "Une erreur est survenue lors de la communication avec l'API" }, { status: 500 });
  }
}

// Helper pour déterminer l'endpoint API à appeler
function determineApiEndpoint(path, params) {
  const { gammeId, marqueId, year } = params;
  
  switch (path) {
    case 'annees':
      return `/api/modele/annees?gammeId=${gammeId}&marqueId=${marqueId}`;
    case 'selection':
      return `/api/modele/selection?gammeId=${gammeId}&marqueId=${marqueId}&year=${year}`;
    default:
      // Par défaut on renvoie vers l'endpoint de base
      const queryString = new URLSearchParams(params).toString();
      return `/api/modele/${path}?${queryString}`;
  }
}
