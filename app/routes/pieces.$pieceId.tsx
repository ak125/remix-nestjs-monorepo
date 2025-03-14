import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData, useNavigate } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { ChevronLeft } from "lucide-react";
import { Alert, AlertTitle, AlertDescription } from "~/components/ui/alert";

export async function loader({ params, request }: LoaderFunctionArgs) {
  try {
    const response = await fetch(
      `${process.env.API_URL}/pieces/${params.pieceId}`,
      { headers: { 'API-Key': process.env.API_KEY || '' } }
    );

    if (!response.ok) {
      throw json({
        message: response.status === 410 
          ? "Cette pièce n'est plus disponible"
          : "Pièce temporairement indisponible"
      }, { 
        status: response.status === 404 ? 412 : response.status 
      });
    }

    return json(await response.json());
  } catch (error) {
    throw json({ message: "Erreur serveur" }, { status: 500 });
  }
}

export default function PieceDetailsPage() {
  const piece = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="bg-white rounded-lg shadow-lg overflow-hidden">
        <div className="p-6">
          <h1 className="text-2xl font-bold mb-4">
            {piece.brand.name} - {piece.name}
          </h1>

          <div className="grid md:grid-cols-2 gap-6">
            <div>
              <h2 className="font-semibold mb-2">Spécifications</h2>
              <dl className="space-y-2">
                <div>
                  <dt className="text-sm text-gray-500">Référence</dt>
                  <dd>{piece.reference}</dd>
                </div>
                {piece.specifications?.map(spec => (
                  <div key={spec.name}>
                    <dt className="text-sm text-gray-500">{spec.name}</dt>
                    <dd>{spec.value}</dd>
                  </div>
                ))}
              </dl>
            </div>

            <div>
              <h2 className="font-semibold mb-2">Prix et Stock</h2>
              {piece.price?.[0] ? (
                <div className="space-y-2">
                  <p className="text-2xl font-bold text-green-600">
                    {piece.price[0].amount.toFixed(2)} €
                  </p>
                  <p className="text-sm text-gray-500">
                    {piece.stock > 0 
                      ? `${piece.stock} unités en stock`
                      : "Rupture de stock"
                    }
                  </p>
                </div>
              ) : (
                <Alert>
                  <AlertTitle>Prix indisponible</AlertTitle>
                  <AlertDescription>
                    Le prix de cette pièce est temporairement indisponible
                  </AlertDescription>
                </Alert>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
