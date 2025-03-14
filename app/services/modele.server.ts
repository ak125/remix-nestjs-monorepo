import { 
  Modele, 
  ModeleFilters, 
  CreateModeleData, 
  UpdateModeleData,
  PaginatedResponse,
  YearOption
} from "~/types/modele.types";

const API_BASE_URL = process.env.API_BASE_URL || "http://localhost:3000";

/**
 * Récupère les modèles avec pagination et filtres
 */
export async function fetchModeles(filters: ModeleFilters): Promise<PaginatedResponse<Modele>> {
  const queryParams = new URLSearchParams();
  
  if (filters.page) queryParams.set("page", filters.page);
  if (filters.limit) queryParams.set("limit", filters.limit);
  if (filters.search) queryParams.set("search", filters.search);
  if (filters.marqueId) queryParams.set("marqueId", filters.marqueId);
  if (filters.yearFrom) queryParams.set("yearFrom", filters.yearFrom);
  if (filters.yearTo) queryParams.set("yearTo", filters.yearTo);
  
  const response = await fetch(`${API_BASE_URL}/api/modele/all?${queryParams}`);
  
  if (!response.ok) {
    throw new Error(`Error fetching modeles: ${response.statusText}`);
  }
  
  return response.json();
}

/**
 * Récupère un modèle par son ID
 */
export async function fetchModeleById(id: string): Promise<Modele> {
  const response = await fetch(`${API_BASE_URL}/api/modele/${id}`);
  
  if (!response.ok) {
    throw new Error(`Error fetching modele: ${response.statusText}`);
  }
  
  return response.json();
}

/**
 * Récupère les années disponibles pour une gamme et marque
 */
export async function fetchYearsForSelection(gammeId: string, marqueId: string): Promise<YearOption[]> {
  const response = await fetch(`${API_BASE_URL}/api/modele/annees?gammeId=${gammeId}&marqueId=${marqueId}`);
  
  if (!response.ok) {
    throw new Error(`Error fetching years: ${response.statusText}`);
  }
  
  return response.json();
}

/**
 * Récupère les modèles disponibles pour une gamme, marque et année
 */
export async function fetchModelesBySelection(gammeId: string, marqueId: string, year: string): Promise<Modele[]> {
  const response = await fetch(
    `${API_BASE_URL}/api/modele/selection?gammeId=${gammeId}&marqueId=${marqueId}&year=${year}`
  );
  
  if (!response.ok) {
    throw new Error(`Error fetching modeles by selection: ${response.statusText}`);
  }
  
  return response.json();
}

/**
 * Crée un nouveau modèle
 */
export async function createModele(data: CreateModeleData): Promise<Modele> {
  const response = await fetch(`${API_BASE_URL}/api/modele`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });
  
  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.message || "Error creating modele");
  }
  
  return response.json();
}

/**
 * Met à jour un modèle existant
 */
export async function updateModele(id: string, data: UpdateModeleData): Promise<Modele> {
  const response = await fetch(`${API_BASE_URL}/api/modele/${id}`, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });
  
  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.message || "Error updating modele");
  }
  
  return response.json();
}

/**
 * Supprime un modèle
 */
export async function deleteModele(id: string): Promise<void> {
  const response = await fetch(`${API_BASE_URL}/api/modele/${id}`, {
    method: "DELETE",
  });
  
  if (!response.ok) {
    const errorData = await response.json();
    throw new Error(errorData.message || "Error deleting modele");
  }
}

/**
 * Vérifie la compatibilité d'une combinaison gamme-marque-année
 */
export async function checkCompatibility(gammeId: string, marqueId: string, year: string): Promise<{
  compatible: boolean;
  count: number;
  message: string;
}> {
  const response = await fetch(
    `${API_BASE_URL}/api/modele/compatibility?gammeId=${gammeId}&marqueId=${marqueId}&year=${year}`
  );
  
  if (!response.ok) {
    throw new Error(`Error checking compatibility: ${response.statusText}`);
  }
  
  return response.json();
}
