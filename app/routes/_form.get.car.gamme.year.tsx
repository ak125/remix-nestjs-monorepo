import { json } from "@remix-run/node";
import { cache } from "~/lib/cache.server";

/**
 * Cette route émule le comportement du fichier PHP _form.get.car.gamme.year.php
 * Elle renvoie des options HTML directement intégrables dans un formulaire
 */
export async function loader({ request }) {
  const url = new URL(request.url);
  const formGammeid = url.searchParams.get("formGammeid");
  const formCarMarqueid = url.searchParams.get("formCarMarqueid");
  
  // Si les paramètres essentiels sont manquants, renvoyer les options par défaut
  if (!formGammeid || !formCarMarqueid) {
    return new Response(`<option value="0">Année</option>`, {
      headers: {
        "Content-Type": "text/html",
        "X-Robots-Tag": "noindex, nofollow"
      }
    });
  }
  
  // Clé de cache spécifique pour ce format de réponse
  const cacheKey = `form-car-year:${formGammeid}:${formCarMarqueid}`;
  
  try {
    // Vérifier le cache
    const cachedHtml = await cache.get(cacheKey);
    if (cachedHtml) {
      return new Response(cachedHtml, {
        headers: {
          "Content-Type": "text/html",
          "X-Robots-Tag": "noindex, nofollow"
        }
      });
    }
    
    // Récupérer les années depuis l'API
    const apiBaseUrl = process.env.API_BASE_URL || 'http://localhost:3000';
    const response = await fetch(
      `${apiBaseUrl}/api/models/years?gammeId=${formGammeid}&marqueId=${formCarMarqueid}`
    );
    
    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }
    
    const years = await response.json();
    
    // Générer le HTML des options
    let htmlOptions = '<option value="0">Année</option>';
    
    if (Array.isArray(years)) {
      years.forEach(year => {
        if (year.favorite) {
          htmlOptions += `<option value="${year.value}" class="favorite">${year.value}</option>`;
        } else {
          htmlOptions += `<option value="${year.value}">${year.value}</option>`;
        }
      });
    }
    
    // Mettre en cache pour 10 minutes
    await cache.set(cacheKey, htmlOptions, 600);
    
    return new Response(htmlOptions, {
      headers: {
        "Content-Type": "text/html",
        "X-Robots-Tag": "noindex, nofollow"
      }
    });
  } catch (error) {
    console.error("Error generating year options:", error);
    
    // En cas d'erreur, retourner juste l'option par défaut
    return new Response(`<option value="0">Année</option>`, {
      headers: {
        "Content-Type": "text/html",
        "X-Robots-Tag": "noindex, nofollow"
      }
    });
  }
}