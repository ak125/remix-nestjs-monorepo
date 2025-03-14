import { json, LoaderFunctionArgs, MetaFunction } from "@remix-run/node";
import { useLoaderData, useSearchParams } from "@remix-run/react";
import { useState } from "react";
import { PieceTechnicalModal } from "~/components/modals";
import { Button } from "~/components/ui/button";
import { prisma } from "~/lib/prisma.server";

export const meta: MetaFunction = () => {
  return [{ title: "Recherche de pièces détachées" }];
};

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const query = url.searchParams.get("q") || "";
  const type = url.searchParams.get("type") || undefined;

  const pieces = await prisma.piece.findMany({
    where: {
      OR: [
        { PIECE_REF: { contains: query } },
        { PIECE_REF_CLEAN: { contains: query } }
      ],
      PIECE_DISPLAY: true,
      ...(type && { PIECE_FIL_ID: Number(type) })
    },
    include: {
      marque: true,
      images: {
        where: { PMI_DISPLAY: true },
        take: 1
      },
      prices: {
        where: { PRI_DISPO: true }
      }
    },
    orderBy: {
      PIECE_SORT: 'asc'
    }
  });

  return json({ pieces });
}

export default function SearchPage() {
  const { pieces } = useLoaderData<typeof loader>();
  const [params] = useSearchParams();
  const [selectedPiece, setSelectedPiece] = useState<string>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-bold">
          Résultats pour: {params.get("q")}
        </h1>
        
        <div className="text-sm text-gray-500">
          {pieces.length} pièce(s) trouvée(s)
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {pieces.map(piece => (
          <div key={piece.PIECE_ID} className="bg-white rounded-lg shadow-md p-4">
            <div className="aspect-square rounded-lg bg-gray-100 mb-4">
              {piece.images[0] ? (
                <img
                  src={`/images/${piece.images[0].PMI_NAME}`}
                  alt={piece.PIECE_NAME}
                  className="w-full h-full object-contain"
                />
              ) : (
                <div className="w-full h-full flex items-center justify-center">
                  <span className="text-gray-400">Pas d'image</span>
                </div>
              )}
            </div>

            <h2 className="font-medium mb-2">
              {piece.PIECE_NAME} {piece.marque.PM_NAME}
            </h2>

            <p className="text-sm text-gray-600 mb-4">
              Réf: {piece.PIECE_REF}
            </p>

            <div className="flex items-center justify-between">
              <Button
                onClick={() => setSelectedPiece(piece.PIECE_ID.toString())}
              >
                Voir la fiche
              </Button>

              {piece.prices[0] && (
                <span className="font-bold">
                  {piece.prices[0].PRI_VENTE_TTC.toFixed(2)} €
                </span>
              )}
            </div>
          </div>
        ))}
      </div>

      <PieceTechnicalModal
        pieceId={selectedPiece}
        isOpen={!!selectedPiece}
        onClose={() => setSelectedPiece(undefined)}
      />
    </div>
  );
}
