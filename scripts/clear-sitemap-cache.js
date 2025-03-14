#!/usr/bin/env node

const axios = require('axios');
const dotenv = require('dotenv');

// Charger les variables d'environnement
dotenv.config();

async function clearSitemapCache() {
  const apiBaseUrl = process.env.API_BASE_URL || 'http://localhost:3000';
  
  try {
    console.log('🔄 Invalidation du cache des sitemaps...');
    const response = await axios.post(`${apiBaseUrl}/api/sitemap/clear-cache`);
    
    if (response.data.success) {
      console.log('✅ Cache des sitemaps invalidé avec succès !');
    } else {
      console.error('❌ Échec de l\'invalidation du cache:', response.data.message);
    }
  } catch (error) {
    console.error('❌ Erreur lors de l\'invalidation du cache:', error.message);
    process.exit(1);
  }
}

clearSitemapCache();
