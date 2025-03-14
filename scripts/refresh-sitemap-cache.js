#!/usr/bin/env node

const axios = require('axios');
const dotenv = require('dotenv');

// Charger les variables d'environnement
dotenv.config();

async function refreshSitemapCache() {
  const apiBaseUrl = process.env.API_BASE_URL || 'http://localhost:3000';
  
  try {
    console.log('🔄 Invalidation du cache des sitemaps et robots.txt...');
    
    // Invalidation du cache des sitemaps
    const sitemapResponse = await axios.post(`${apiBaseUrl}/api/sitemap/clear-cache`);
    
    // Forcer la régénération du sitemap principal
    await axios.get(`${apiBaseUrl}/sitemap.xml`);
    
    // Forcer la régénération des URLs désactivées
    await axios.get(`${apiBaseUrl}/api/robots/disallowed-urls`);
    
    if (sitemapResponse.data.success) {
      console.log('✅ Cache des sitemaps invalidé avec succès !');
    } else {
      console.error('❌ Échec de l\'invalidation du cache:', sitemapResponse.data.message);
    }
  } catch (error) {
    console.error('❌ Erreur lors de l\'invalidation du cache:', error.message);
    process.exit(1);
  }
}

// Exécuter le script
refreshSitemapCache();
