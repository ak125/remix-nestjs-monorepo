import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "~/components/ui/card";
import { Button } from "~/components/ui/button";
import { Link } from "@remix-run/react";

interface GammeCardProps {
  gamme: {
    id: number;
    name: string;
    alias: string;
    image: string;
  };
  isFavorite: boolean;
  onFavoriteToggle: () => void;
}

export function GammeCard({ gamme, isFavorite, onFavoriteToggle }: GammeCardProps) {
  return (
    <Card className="overflow-hidden transition-all hover:shadow-lg">
      <Link to={`/searchcar/${gamme.alias}-${gamme.id}`}>
        <CardHeader className="p-0">
          <img
            src={gamme.image}
            alt={gamme.name}
            className="w-full h-48 object-cover"
            loading="lazy"
          />
        </CardHeader>
        
        <CardContent className="p-4">
          <CardTitle className="text-lg font-medium">
            {gamme.name}
          </CardTitle>
        </CardContent>
      </Link>
      
      <CardFooter className="p-4 pt-0">
        <Button
          variant={isFavorite ? "secondary" : "outline"}
          className="w-full"
          onClick={onFavoriteToggle}
        >
          {isFavorite ? "❤️ Favori" : "🤍 Ajouter aux favoris"}
        </Button>
      </CardFooter>
    </Card>
  );
}
