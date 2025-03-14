import { LoaderFunction } from "@remix-run/node";
import { cache } from "~/lib/cache.server";

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const type = url.searchParams.get("type");
  
  // Créer une clé de cache unique pour chaque type de sitemap
  const cacheKey = `sitemap:${type || 'main'}`;
  
  try {
    // Vérifier si le sitemap est déjà en cache
    const cachedSitemap = await cache.get(cacheKey);
    if (cachedSitemap) {
      return new Response(cachedSitemap, {
        headers: {
          "Content-Type": "application/xml",
          "Cache-Control": "public, max-age=3600"
        }
      });
    }
    
    // Si pas en cache, récupérer depuis l'API NestJS
    const apiUrl = `${process.env.API_BASE_URL || 'http://localhost:3000'}/sitemap.xml${type ? `?type=${type}` : ''}`;
    const response = await fetch(apiUrl);
    
    if (!response.ok) {
      throw new Error(`Erreur lors de la récupération du sitemap: ${response.status}`);
    }
    
    const sitemap = await response.text();
    
    // Mettre en cache pour 1 heure
    await cache.set(cacheKey, sitemap, 60 * 60);
    
    return new Response(sitemap, {
      headers: {
        "Content-Type": "application/xml",
        "Cache-Control": "public, max-age=3600"
      }
    });
  } catch (error) {
    console.error("Erreur lors de la génération du sitemap:", error);
    
    // En cas d'erreur, générer un sitemap minimal
    const baseUrl = process.env.BASE_URL || 'https://example.com';
    const fallbackSitemap = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>${baseUrl}/</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
</urlset>`;
    
    return new Response(fallbackSitemap, {
      headers: {
        "Content-Type": "application/xml",
        "Cache-Control": "public, max-age=3600"
      }
    });
  }
};

export default function Sitemap() {
  // Cette route ne rend aucun contenu visuel
  return null;
}
