export const API_URL = "http://localhost:3000";

export async function fetchAPI(endpoint: string, options?: RequestInit) {
  const response = await fetch(`${API_URL}${endpoint}`, {
    credentials: "include",
    ...options,
  });

  if (!response.ok) {
    throw new Error("Erreur lors de la récupération des données");
  }

  return response.json();
}
