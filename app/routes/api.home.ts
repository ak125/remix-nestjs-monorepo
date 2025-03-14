import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export async function loader() {
  try {
    // Utiliser le cache pour de meilleures performances
    const cachedData = await cache.get("homepage-data");
    if (cachedData) {
      return json(cachedData);
    }
    
    // Récupérer les données pour la page d'accueil
    const [marques, catalogFamilies, equipementiers, topGammes] = await Promise.all([
      // Marques automobiles à afficher
      prisma.marque.findMany({
        where: { 
          display: true,
          NOT: { id: { in: [339, 441] } } // Exclure certaines marques comme dans le code PHP
        },
        orderBy: { sort: 'asc' },
        select: {
          id: true,
          name: true,
          nameMeta: true,
          alias: true,
          logo: true,
          top: true
        }
      }),
      
      // Familles de catalogue avec leurs gammes
      prisma.catalogFamily.findMany({
        where: { display: true },
        select: {
          id: true,
          name: true,
          nameSystem: true,
          description: true,
          image: true,
          sort: true,
          gammes: {
            where: {
              gamme: {
                display: true,
                level: 1
              }
            },
            include: {
              gamme: {
                select: {
                  id: true,
                  name: true,
                  nameMeta: true,
                  nameUrl: true,
                  alias: true,
                  image: true
                }
              }
            },
            orderBy: { sort: 'asc' }
          }
        },
        orderBy: { sort: 'asc' }
      }),
      
      // Équipementiers mis en avant
      prisma.equipementier.findMany({
        where: { 
          display: true,
          top: true
        },
        orderBy: { sort: 'asc' },
        select: {
          id: true,
          name: true,
          nameMeta: true,
          logo: true,
          preview: true
        }
      }),
      
      // Gammes de produits mises en avant
      prisma.catalogGamme.findMany({
        where: {
          display: true,
          level: 1,
          top: true
        },
        include: {
          seo: true
        },
        orderBy: {
          // Pas de tri spécifique dans le code original, on utilise l'ID
          id: 'asc'
        }
      })
    ]);

    // Enrichir les top gammes avec les conseils du blog
    const topGammeIds = topGammes.map(gamme => gamme.id);
    
    const blogAdvices = await prisma.blogAdvice.findMany({
      where: {
        gammeId: { in: topGammeIds }
      }
    });
    
    // Associer les conseils blog aux gammes correspondantes
    const enrichedTopGammes = topGammes.map(gamme => {
      const advice = blogAdvices.find(advice => advice.gammeId === gamme.id);
      return {
        ...gamme,
        blogAdvice: advice || null
      };
    });
    
    // Données à renvoyer
    const data = {
      marques,
      catalogFamilies,
      equipementiers,
      topGammes: enrichedTopGammes
    };
    
    // Mettre en cache pour 5 minutes
    await cache.set("homepage-data", data, 300);
    
    return json(data);
  } catch (error) {
    console.error("Erreur lors de la récupération des données de la page d'accueil:", error);
    return json({ error: "Une erreur est survenue" }, { status: 500 });
  }
}
