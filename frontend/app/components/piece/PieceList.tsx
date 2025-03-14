import { Card, CardContent, CardFooter, CardHeader } from "~/components/ui/card";
import { Button } from "~/components/ui/button";
import { Badge } from "~/components/ui/badge";
import { useFetcher } from "@remix-run/react";

interface PieceListProps {
  pieces: Array<{
    id: number;
    name: string;
    reference: string;
    equipement: {
      name: string;
      logo: string;
    };
    prices: Array<{
      price: number;
      isPromo: boolean;
    }>;
    specifications?: Record<string, string>;
  }>;
}

export function PieceList({ pieces }: PieceListProps) {
  const fetcher = useFetcher();

  const addToCart = (pieceId: number) => {
    fetcher.submit(
      { pieceId: String(pieceId) },
      { method: "post", action: "/api/cart" }
    );
  };

  return (
    <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
      {pieces.map(piece => (
        <Card key={piece.id} className="overflow-hidden">
          <CardHeader className="p-0">
            <img
              src={piece.equipement.logo}
              alt={piece.equipement.name}
              className="w-full h-48 object-contain p-4"
            />
          </CardHeader>
          
          <CardContent className="p-4">
            <h3 className="font-medium">{piece.name}</h3>
            <p className="text-sm text-muted-foreground">{piece.reference}</p>
            
            {piece.specifications && (
              <div className="mt-2 space-y-1">
                {Object.entries(piece.specifications).map(([key, value]) => (
                  <Badge key={key} variant="secondary" className="mr-2">
                    {key}: {value}
                  </Badge>
                ))}
              </div>
            )}
          </CardContent>

          <CardFooter className="p-4 pt-0 flex justify-between items-center">
            <div className="text-lg font-bold">
              {piece.prices[0].price} €
              {piece.prices[0].isPromo && (
                <Badge variant="destructive" className="ml-2">Promo</Badge>
              )}
            </div>
            <Button onClick={() => addToCart(piece.id)}>
              Ajouter
            </Button>
          </CardFooter>
        </Card>
      ))}
    </div>
  );
}
