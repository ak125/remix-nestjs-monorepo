import { json, LoaderFunction } from "@remix-run/node";
import { prisma } from "~/lib/prisma.server";

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const marqueId = parseInt(url.searchParams.get("marqueId") || "0", 10);
  const year = parseInt(url.searchParams.get("year") || "0", 10);
  const gammeId = parseInt(url.searchParams.get("gammeId") || "0", 10);

  if (!marqueId || !year || !gammeId) {
    return json({ models: [] });
  }

  const models = await prisma.autoModele.findMany({
    where: {
      marqueId,
      isDisplayed: true,
      types: {
        some: {
          yearFrom: { lte: year },
          yearTo: { gte: year },
          isDisplayed: true
        }
      }
    },
    select: {
      id: true,
      name: true,
      specifications: true
    },
    orderBy: {
      name: 'asc'
    }
  });

  return json({ models });
};
