import { logError } from "./logger.server";
import { prisma } from "./database.server";
import axios from "axios";

interface SearchEngine {
  name: string;
  pingUrl: string;
}

export async function notifySearchEngines() {
  const sitemapUrl = encodeURIComponent("https://www.automecanik.com/sitemap.xml");
  
  const engines: SearchEngine[] = [
    { name: "Google", pingUrl: `https://www.google.com/ping?sitemap=${sitemapUrl}` },
    { name: "Bing", pingUrl: `https://www.bing.com/ping?sitemap=${sitemapUrl}` },
    { name: "Yahoo", pingUrl: `https://search.yahoo.com/ping?sitemap=${sitemapUrl}` },
    { name: "Yandex", pingUrl: `https://yandex.com/indexnow?url=${sitemapUrl}` },
  ];

  const results = await Promise.allSettled(
    engines.map(async (engine) => {
      try {
        const response = await axios.get(engine.pingUrl, { timeout: 5000 });
        
        // Log du succès
        await prisma.sitemapNotificationLog.create({
          data: {
            engine: engine.name,
            statusCode: response.status,
            message: "Notification réussie",
          }
        });
        
        console.log(`✅ Sitemap notifié avec succès à ${engine.name}`);
        return { name: engine.name, success: true };
      } catch (error) {
        const statusCode = error.response?.status || 500;
        const errorMessage = error.message || "Erreur inconnue";
        
        // Log de l'erreur
        await prisma.sitemapNotificationLog.create({
          data: {
            engine: engine.name,
            statusCode,
            message: errorMessage,
          }
        });
        
        console.error(`❌ Erreur de notification à ${engine.name}: ${errorMessage}`);
        return { name: engine.name, success: false, error: errorMessage };
      }
    })
  );

  // Vérification si des erreurs ont été rencontrées
  const failures = results.filter(
    (result) => result.status === "rejected" || (result.status === "fulfilled" && !result.value.success)
  );
  
  if (failures.length > 0) {
    const failedEngines = failures
      .map((result) => 
        result.status === "fulfilled" 
          ? `${result.value.name} (${result.value.error})` 
          : "Erreur inconnue"
      )
      .join(", ");
      
    await logError(`Échec de notification du sitemap aux moteurs de recherche: ${failedEngines}`);
  }

  return results;
}

// Fonction pour notifier uniquement un moteur spécifique
export async function notifySingleSearchEngine(engineName: string) {
  const sitemapUrl = encodeURIComponent("https://www.automecanik.com/sitemap.xml");
  let pingUrl: string;
  
  switch (engineName.toLowerCase()) {
    case "google":
      pingUrl = `https://www.google.com/ping?sitemap=${sitemapUrl}`;
      break;
    case "bing":
      pingUrl = `https://www.bing.com/ping?sitemap=${sitemapUrl}`;
      break;
    case "yahoo":
      pingUrl = `https://search.yahoo.com/ping?sitemap=${sitemapUrl}`;
      break;
    case "yandex":
      pingUrl = `https://yandex.com/indexnow?url=${sitemapUrl}`;
      break;
    default:
      throw new Error(`Moteur de recherche non pris en charge: ${engineName}`);
  }

  try {
    const response = await axios.get(pingUrl, { timeout: 5000 });
    await prisma.sitemapNotificationLog.create({
      data: {
        engine: engineName,
        statusCode: response.status,
        message: "Notification réussie",
      }
    });
    
    return { success: true, status: response.status };
  } catch (error) {
    const statusCode = error.response?.status || 500;
    await prisma.sitemapNotificationLog.create({
      data: {
        engine: engineName,
        statusCode,
        message: error.message || "Erreur inconnue",
      }
    });
    
    return { success: false, status: statusCode, error: error.message };
  }
}
