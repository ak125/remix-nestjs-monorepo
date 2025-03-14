import { json, LoaderFunction } from "@remix-run/node";
import { PaginatedResponse, Modele } from "~/types/modele.types";

interface LoaderData {
  data: Modele[];
  pagination: {
    total: number;
    page: number;
    limit: number;
    totalPages: number;
  };
}

export const loader: LoaderFunction = async ({ request }): Promise<Response> => {
  const url = new URL(request.url);
  const carMarqueId = url.searchParams.get("carMarqueId") || "";
  const gammeId = url.searchParams.get("gammeId") || "";
  const search = url.searchParams.get("search") || "";
  const page = url.searchParams.get("page") || "1";
  const limit = url.searchParams.get("limit") || "10";

  try {
    // Construction de l'URL avec tous les paramètres
    const params = new URLSearchParams({
      ...(carMarqueId && { carMarqueId }),
      ...(gammeId && { gammeId }),
      ...(search && { search }),
      page,
      limit
    });

    // Appel à l'API NestJS
    const apiBaseUrl = process.env.API_BASE_URL || 'http://localhost:3000';
    const response = await fetch(`${apiBaseUrl}/api/models?${params.toString()}`);

    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }

    // Récupération et formatage de la réponse
    const data: PaginatedResponse<Modele> = await response.json();
    
    return json(data);
  } catch (error) {
    console.error("Error fetching models:", error);
    return json({ 
      data: [],
      pagination: { page: 1, limit: 10, total: 0, totalPages: 1 },
      error: error instanceof Error ? error.message : "Une erreur est survenue" 
    });
  }
};

// Cette route n'a pas de composant par défaut car elle sert uniquement d'API
export default function ModelsAPI() {
  return null;
}
