import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export async function loader({ request }) {
  try {
    // Vérifier le cache d'abord
    const cachedData = await cache.get("equipementiers-list");
    if (cachedData) {
      return json(cachedData);
    }
    
    // Récupérer les équipementiers depuis la base de données
    const equipementiers = await prisma.equipementier.findMany({
      where: { 
        display: true,
        // Afficher uniquement les équipementiers mis en avant si le paramètre est présent
        ...(new URL(request.url).searchParams.has("top") && {
          top: true
        })
      },
      orderBy: { sort: 'asc' },
      select: {
        id: true,
        name: true,
        nameMeta: true,
        logo: true,
        preview: true
      }
    });
    
    // Mettre en cache pour 5 minutes
    await cache.set("equipementiers-list", equipementiers, 300);
    
    return json(equipementiers);
  } catch (error) {
    console.error("Error fetching equipementiers:", error);
    return json({ error: "Une erreur est survenue" }, { status: 500 });
  }
}
