import type { LoaderFunction } from "@remix-run/node";
import { prisma } from "~/utils/database.server";
import { formatUrlForSeo } from "~/utils/seo.server";
import { notifySearchEngines } from "~/utils/notifySearchEngines.server";

function generateSitemapEntry(url: string, lastmod?: Date, priority = 0.5, changefreq = "weekly") {
  return `
  <url>
    <loc>${formatUrlForSeo(url)}</loc>
    ${lastmod ? `<lastmod>${lastmod.toISOString()}</lastmod>` : ''}
    <priority>${priority}</priority>
    <changefreq>${changefreq}</changefreq>
  </url>`;
}

export const loader: LoaderFunction = async ({ request }) => {
  // Pages statiques
  const staticPages = [
    { url: "https://www.automecanik.com/", priority: 1.0, changefreq: "daily" },
    { url: "https://www.automecanik.com/blog", priority: 0.9, changefreq: "daily" },
    { url: "https://www.automecanik.com/contact", priority: 0.6, changefreq: "monthly" },
  ];

  // Récupération des modèles actifs
  const marques = await prisma.marque.findMany({
    where: { display: true },
    include: {
      modeles: {
        where: { display: true },
      },
    },
    orderBy: { name: "asc" },
  });

  const entries = [
    // Pages statiques
    ...staticPages.map(page => 
      generateSitemapEntry(page.url, new Date(), page.priority, page.changefreq)
    ),

    // Pages des marques
    ...marques.map(marque => 
      generateSitemapEntry(
        `https://www.automecanik.com/blog/${marque.alias}`,
        new Date(),
        0.8,
        "weekly"
      )
    ),

    // Pages des modèles
    ...marques.flatMap(marque => 
      marque.modeles.map(modele => 
        generateSitemapEntry(
          `https://www.automecanik.com/blog/${marque.alias}/${modele.alias}`,
          new Date(),
          0.8,
          "weekly"
        )
      )
    ),
  ];

  // Si la requête contient notify=true, on notifie les moteurs de recherche
  const url = new URL(request.url);
  if (url.searchParams.get("notify") === "true") {
    // Notification asynchrone pour ne pas bloquer la réponse
    notifySearchEngines().catch(console.error);
  }

  return new Response(
    `<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
      ${entries.join("\n")}
    </urlset>`,
    {
      status: 200,
      headers: {
        "Content-Type": "application/xml",
        "Cache-Control": "public, max-age=3600",
        "X-Robots-Tag": "noindex",
      },
    }
  );
};
