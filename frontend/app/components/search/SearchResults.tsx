import { GammeCard } from "~/components/catalog/GammeCard";

interface SearchResultsProps {
  results: Array<{
    id: number;
    name: string;
    alias: string;
    image: string;
    category: {
      name: string;
    };
  }>;
}

export function SearchResults({ results }: SearchResultsProps) {
  if (results.length === 0) {
    return (
      <div className="text-center py-8">
        <p className="text-muted-foreground">Aucun résultat trouvé</p>
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      {results.map(gamme => (
        <GammeCard
          key={gamme.id}
          gamme={gamme}
          category={gamme.category}
          showCategory
        />
      ))}
    </div>
  );
}
