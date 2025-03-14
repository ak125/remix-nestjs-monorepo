import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { cache } from "~/lib/cache.server";

interface RobotRule {
  userAgent: string;
  disallow: string[];
}

interface SitemapConfig {
  url: string;
  priority?: string;
}

interface RobotsConfig {
  rules: RobotRule[];
  sitemaps: SitemapConfig[];
  crawlDelay?: number;
  host?: string;
}

export const loader: LoaderFunction = async ({ request }) => {
  // Récupérer l'environnement actuel
  const isProduction = process.env.NODE_ENV === "production";
  const baseUrl = process.env.BASE_URL || "https://example.com";
  
  try {
    // Essayer de récupérer la configuration depuis l'API NestJS
    const cacheKey = "robots-txt-config";
    const disallowedUrlsCacheKey = "robots-txt-disallowed";
    
    // Récupérer la configuration et les URLs désactivées en parallèle
    let [robotsConfig, disallowedUrls] = await Promise.all([
      cache.get(cacheKey) as RobotsConfig | null,
      cache.get(disallowedUrlsCacheKey) as string[] | null
    ]);
    
    // Récupérer la configuration si pas en cache
    if (!robotsConfig) {
      const apiUrl = `${process.env.API_BASE_URL || 'http://localhost:3000'}/api/robots/config`;
      const response = await fetch(apiUrl);
      
      if (!response.ok) {
        throw new Error(`Échec de récupération de la configuration robots.txt: ${response.status}`);
      }
      
      robotsConfig = await response.json();
      
      // Mettre en cache pour 1 heure
      await cache.set(cacheKey, robotsConfig, 60 * 60);
    }
    
    // Récupérer les URLs désactivées si pas en cache
    if (!disallowedUrls) {
      const apiUrl = `${process.env.API_BASE_URL || 'http://localhost:3000'}/api/robots/disallowed-urls`;
      const response = await fetch(apiUrl);
      
      if (response.ok) {
        const data = await response.json();
        disallowedUrls = data.urls || [];
        
        // Mettre en cache pour 15 minutes
        await cache.set(disallowedUrlsCacheKey, disallowedUrls, 15 * 60);
      } else {
        disallowedUrls = [];
      }
    }
    
    // Si en environnement de développement, tout bloquer
    if (!isProduction) {
      robotsConfig = {
        rules: [
          {
            userAgent: "*",
            disallow: ["/"]
          }
        ],
        sitemaps: []
      };
    }
    
    // Générer le contenu du robots.txt
    let robotsTxt = "";
    
    // Ajouter les règles pour chaque user-agent
    robotsConfig.rules.forEach(rule => {
      robotsTxt += `User-agent: ${rule.userAgent}\n`;
      
      // Ajouter les règles Disallow
      if (rule.disallow && rule.disallow.length > 0) {
        rule.disallow.forEach(path => {
          robotsTxt += `Disallow: ${path}\n`;
        });
      }
      
      // Ajouter les URLs désactivées dynamiquement (seulement pour le user-agent *)
      if (rule.userAgent === "*" && disallowedUrls && disallowedUrls.length > 0) {
        disallowedUrls.forEach(url => {
          robotsTxt += `Disallow: ${url}\n`;
        });
      }
      
      robotsTxt += "\n";
    });
    
    // Ajouter le crawl delay si défini
    if (robotsConfig.crawlDelay) {
      robotsTxt += `Crawl-delay: ${robotsConfig.crawlDelay}\n\n`;
    }
    
    // Ajouter le host si défini
    if (robotsConfig.host) {
      robotsTxt += `Host: ${robotsConfig.host}\n\n`;
    }
    
    // Ajouter le sitemap index au lieu des sitemaps individuels
    robotsTxt += `Sitemap: ${baseUrl}/sitemap-index.xml\n`;
    
    // Retourner le contenu généré
    return new Response(robotsTxt.trim(), {
      headers: {
        "Content-Type": "text/plain",
        "Cache-Control": "public, max-age=3600"
      }
    });
  } catch (error) {
    console.error("Erreur lors de la génération du robots.txt:", error);
    
    // En cas d'erreur, retourner une configuration par défaut
    const defaultRobotsTxt = `
User-agent: *
${!isProduction ? "Disallow: /" : "Disallow: /admin/\nDisallow: /login/\nDisallow: /checkout/"}

${isProduction ? `Sitemap: ${baseUrl}/sitemap.xml` : ""}
    `.trim();
    
    return new Response(defaultRobotsTxt, {
      headers: {
        "Content-Type": "text/plain",
      }
    });
  }
};

// La page elle-même ne rend rien car c'est juste un fichier texte
export default function Robots() {
  return null;
}
