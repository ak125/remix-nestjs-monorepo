import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData, useSearchParams } from "@remix-run/react";
import { PieceFilters } from "~/components/piece/PieceFilters";
import { PieceList } from "~/components/piece/PieceList";
import { prisma } from "~/lib/prisma.server";

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const filters = {
    typeId: Number(url.searchParams.get("typeId")),
    marqueId: Number(url.searchParams.get("marqueId")), 
    modeleId: Number(url.searchParams.get("modeleId")),
    gammeId: Number(url.searchParams.get("gammeId")),
    equipementId: url.searchParams.get("equipementId") ? 
      Number(url.searchParams.get("equipementId")) : undefined,
    essieuId: url.searchParams.get("essieuId") ?
      Number(url.searchParams.get("essieuId")) : undefined
  };

  const [pieces, filterOptions] = await Promise.all([
    prisma.piece.findMany({
      where: {
        typeId: filters.typeId,
        marqueId: filters.marqueId,
        modeleId: filters.modeleId,
        gammeId: filters.gammeId,
        equipementId: filters.equipementId,
        essieuId: filters.essieuId,
        display: true
      },
      include: {
        equipement: true,
        prices: {
          where: { isActive: true }
        }
      }
    }),
    prisma.equipement.findMany({
      where: { isActive: true }
    })
  ]);

  return json({ pieces, filterOptions });
};

export default function PiecesPage() {
  const { pieces, filterOptions } = useLoaderData<typeof loader>();
  const [searchParams] = useSearchParams();

  return (
    <div className="container mx-auto p-4">
      <PieceFilters 
        options={filterOptions}
        defaultValues={Object.fromEntries(searchParams)}
      />
      <PieceList pieces={pieces} />
    </div>
  );
}
