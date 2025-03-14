import { useState } from "react";
import { Card } from "~/components/ui/card";
import { Button } from "~/components/ui/button";
import { useFetcher } from "@remix-run/react";
import { GammeCard } from "./GammeCard";

interface CategoryListProps {
  categories: Array<{
    id: number;
    name: string;
    gammes: Array<{
      id: number;
      name: string;
      alias: string;
      image: string;
    }>;
  }>;
}

export function CategoryList({ categories }: CategoryListProps) {
  const [favorites, setFavorites] = useState<number[]>([]);
  const fetcher = useFetcher();

  const toggleFavorite = (gammeId: number) => {
    const newFavorites = favorites.includes(gammeId)
      ? favorites.filter(id => id !== gammeId)
      : [...favorites, gammeId];
    
    setFavorites(newFavorites);
    
    // Synchroniser avec le backend si l'utilisateur est connecté
    fetcher.submit(
      { gammeId: String(gammeId) },
      { method: "post", action: "/api/favorites" }
    );
  };

  return (
    <div className="space-y-8">
      {categories.map(category => (
        <section key={category.id} className="space-y-4">
          <h2 className="text-xl font-semibold tracking-tight">
            {category.name}
          </h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {category.gammes.map(gamme => (
              <GammeCard
                key={gamme.id}
                gamme={gamme}
                isFavorite={favorites.includes(gamme.id)}
                onFavoriteToggle={() => toggleFavorite(gamme.id)}
              />
            ))}
          </div>
        </section>
      ))}
    </div>
  );
}
