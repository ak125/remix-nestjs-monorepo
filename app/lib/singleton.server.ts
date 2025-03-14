/**
 * Utilitaire pour créer et gérer des instances singleton dans un environnement serveur
 * Cela empêche la création de multiples connexions à des ressources externes
 * comme les bases de données ou Redis lors des redémarrages du serveur en développement
 */

const singletons = new Map<string, unknown>();

export function singleton<T>(name: string, create: () => T): T {
  if (!singletons.has(name)) {
    singletons.set(name, create());
  }
  
  return singletons.get(name) as T;
}

// Fonction pour nettoyer les singletons (utile pour les tests)
export function resetSingletons(): void {
  singletons.clear();
}
