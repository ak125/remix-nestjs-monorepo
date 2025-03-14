#!/usr/bin/env node

const axios = require('axios');
const dotenv = require('dotenv');

// Charger les variables d'environnement
dotenv.config();

async function notifySearchEngines() {
  const apiBaseUrl = process.env.API_BASE_URL || 'http://localhost:3000';
  
  try {
    console.log('🔄 Notification des moteurs de recherche...');
    
    // Endpoint qui rafraîchit le sitemap ET notifie les moteurs de recherche
    const response = await axios.post(`${apiBaseUrl}/api/sitemap/refresh-and-notify`, {}, {
      headers: {
        Authorization: `Bearer ${process.env.API_TOKEN || ''}` // Token d'authentification si nécessaire
      }
    });
    
    if (response.data.success) {
      console.log('✅ Notification réussie !');
      console.log(`ℹ️ Message: ${response.data.message}`);
    } else {
      console.error('❌ Échec de la notification:', response.data.message);
    }
  } catch (error) {
    console.error('❌ Erreur lors de la notification des moteurs de recherche:');
    if (error.response) {
      console.error(`   Status: ${error.response.status}`);
      console.error(`   Message: ${error.response.data.message || JSON.stringify(error.response.data)}`);
    } else {
      console.error(`   ${error.message}`);
    }
    process.exit(1);
  }
}

notifySearchEngines();
