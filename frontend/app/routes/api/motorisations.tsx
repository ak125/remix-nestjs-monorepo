import { json, LoaderFunction } from "@remix-run/node";
import { prisma } from "~/lib/prisma.server";

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const marqueId = parseInt(url.searchParams.get("marqueId") || "0", 10);
  const modeleId = parseInt(url.searchParams.get("modeleId") || "0", 10);
  const year = parseInt(url.searchParams.get("year") || "0", 10);

  if (!marqueId || !modeleId || !year) {
    return json({ groups: [] });
  }

  const types = await prisma.autoType.findMany({
    where: {
      marqueId,
      modeleId,
      yearFrom: { lte: year },
      yearTo: { gte: year },
      isDisplayed: true
    },
    include: {
      specifications: true
    },
    orderBy: [
      { fuel: 'asc' },
      { powerPS: 'desc' }
    ]
  });

  // Grouper par type de carburant
  const grouped = types.reduce((acc, type) => {
    const key = type.fuel;
    if (!acc[key]) acc[key] = [];
    acc[key].push(type);
    return acc;
  }, {});

  return json({
    groups: Object.entries(grouped).map(([fuel, motors]) => ({
      fuel,
      motors: motors.map((m: any) => ({
        id: m.id,
        name: m.name,
        alias: m.alias,
        power: `${m.powerPS} Ch`,
        years: `${m.yearFrom}-${m.yearTo || 'Actuel'}`,
        specs: m.specifications
      }))
    }))
  });
};
