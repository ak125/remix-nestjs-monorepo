import { LoaderFunction } from "@remix-run/node";
import { cache } from "~/lib/cache.server";

export const loader: LoaderFunction = async ({ request }) => {
  // Utiliser une clé de cache pour l'index de sitemap
  const cacheKey = "sitemap:index";
  
  try {
    // Vérifier si l'index est déjà en cache
    const cachedIndex = await cache.get(cacheKey);
    if (cachedIndex) {
      return new Response(cachedIndex, {
        headers: {
          "Content-Type": "application/xml",
          "Cache-Control": "public, max-age=7200"
        }
      });
    }
    
    // Si pas en cache, récupérer depuis l'API NestJS
    const apiUrl = `${process.env.API_BASE_URL || 'http://localhost:3000'}/sitemap-index.xml`;
    const response = await fetch(apiUrl);
    
    if (!response.ok) {
      throw new Error(`Erreur lors de la récupération de l'index de sitemap: ${response.status}`);
    }
    
    const sitemapIndex = await response.text();
    
    // Mettre en cache pour 2 heures
    await cache.set(cacheKey, sitemapIndex, 2 * 60 * 60);
    
    return new Response(sitemapIndex, {
      headers: {
        "Content-Type": "application/xml",
        "Cache-Control": "public, max-age=7200"
      }
    });
  } catch (error) {
    console.error("Erreur lors de la génération de l'index de sitemap:", error);
    
    // En cas d'erreur, générer un index minimal
    const baseUrl = process.env.BASE_URL || 'https://example.com';
    const fallbackIndex = `<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>${baseUrl}/sitemap.xml</loc>
    <lastmod>${new Date().toISOString().split('T')[0]}</lastmod>
  </sitemap>
</sitemapindex>`;
    
    return new Response(fallbackIndex, {
      headers: {
        "Content-Type": "application/xml",
        "Cache-Control": "public, max-age=7200"
      }
    });
  }
};

export default function SitemapIndex() {
  // Cette route ne rend aucun contenu visuel
  return null;
}
