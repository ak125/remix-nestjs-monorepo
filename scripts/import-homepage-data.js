const { PrismaClient } = require('@prisma/client');
const { exit } = require('process');
const path = require('path');
const fs = require('fs');
const mysql = require('mysql2/promise');
require('dotenv').config();

const prisma = new PrismaClient();

async function importFromMySQL() {
  console.log('Connecting to legacy MySQL database...');
  
  // Connexion à la base de données MySQL source
  const connection = await mysql.createConnection({
    host: process.env.LEGACY_DB_HOST || 'localhost',
    user: process.env.LEGACY_DB_USER || 'root',
    password: process.env.LEGACY_DB_PASSWORD || '',
    database: process.env.LEGACY_DB_NAME || 'automecanik_legacy'
  });
  
  console.log('Connected successfully to legacy database');
  
  try {
    console.log('Starting import of homepage data...');
    
    // 1. Marques
    console.log('Importing marques...');
    const [marques] = await connection.execute(
      `SELECT MARQUE_ID, MARQUE_NAME, MARQUE_NAME_META, MARQUE_ALIAS, MARQUE_LOGO, 
      MARQUE_DISPLAY, MARQUE_SORT, MARQUE_TOP 
      FROM AUTO_MARQUE 
      WHERE MARQUE_DISPLAY = 1`
    );
    
    console.log(`Found ${marques.length} marques`);
    
    // Supprimer toutes les marques existantes
    await prisma.marque.deleteMany({});
    
    // Insérer les nouvelles marques
    for (const marque of marques) {
      await prisma.marque.create({
        data: {
          id: marque.MARQUE_ID,
          name: marque.MARQUE_NAME,
          nameMeta: marque.MARQUE_NAME_META || marque.MARQUE_NAME,
          alias: marque.MARQUE_ALIAS,
          logo: marque.MARQUE_LOGO,
          display: marque.MARQUE_DISPLAY === 1,
          sort: marque.MARQUE_SORT || 0,
          top: marque.MARQUE_TOP === 1
        }
      });
    }
    
    // 2. Familles de catalogue
    console.log('Importing catalog families...');
    const [families] = await connection.execute(
      `SELECT MF_ID, MF_NAME, MF_NAME_SYSTEM, MF_DESCRIPTION, MF_PIC, 
      MF_DISPLAY, MF_SORT 
      FROM CATALOG_FAMILY 
      WHERE MF_DISPLAY = 1`
    );
    
    console.log(`Found ${families.length} catalog families`);
    
    // Supprimer toutes les familles existantes
    await prisma.catalogFamily.deleteMany({});
    
    // Insérer les nouvelles familles
    for (const family of families) {
      await prisma.catalogFamily.create({
        data: {
          id: family.MF_ID,
          name: family.MF_NAME,
          nameSystem: family.MF_NAME_SYSTEM,
          description: family.MF_DESCRIPTION,
          image: family.MF_PIC,
          display: family.MF_DISPLAY === 1,
          sort: family.MF_SORT || 0
        }
      });
    }
    
    // 3. Gammes de catalogue
    console.log('Importing catalog gammes...');
    const [gammes] = await connection.execute(
      `SELECT PG_ID, PG_NAME, PG_NAME_URL, PG_NAME_META, PG_ALIAS, PG_IMG, PG_PIC, 
      PG_LEVEL, PG_DISPLAY, PG_TOP 
      FROM PIECES_GAMME 
      WHERE PG_DISPLAY = 1`
    );
    
    console.log(`Found ${gammes.length} catalog gammes`);
    
    // Supprimer toutes les gammes existantes
    await prisma.catalogGamme.deleteMany({});
    
    // Insérer les nouvelles gammes
    for (const gamme of gammes) {
      await prisma.catalogGamme.create({
        data: {
          id: gamme.PG_ID,
          name: gamme.PG_NAME,
          nameUrl: gamme.PG_NAME_URL || gamme.PG_NAME,
          nameMeta: gamme.PG_NAME_META || gamme.PG_NAME,
          alias: gamme.PG_ALIAS,
          image: gamme.PG_IMG || '',
          pic: gamme.PG_PIC,
          level: gamme.PG_LEVEL || 1,
          display: gamme.PG_DISPLAY === 1,
          top: gamme.PG_TOP === 1
        }
      });
    }
    
    // 4. Relations entre familles et gammes
    console.log('Importing catalog gamme relations...');
    const [relations] = await connection.execute(
      `SELECT MC_ID, MC_MF_ID, MC_PG_ID, MC_SORT 
      FROM CATALOG_GAMME`
    );
    
    console.log(`Found ${relations.length} catalog gamme relations`);
    
    // Supprimer toutes les relations existantes
    await prisma.catalogGammeRelation.deleteMany({});
    
    // Insérer les nouvelles relations
    for (const relation of relations) {
      try {
        await prisma.catalogGammeRelation.create({
          data: {
            id: relation.MC_ID,
            familyId: relation.MC_MF_ID,
            gammeId: relation.MC_PG_ID,
            sort: relation.MC_SORT || 0
          }
        });
      } catch (error) {
        // Ignorer les erreurs de clé étrangère pour les relations orphelines
        console.log(`Skipped relation between family ${relation.MC_MF_ID} and gamme ${relation.MC_PG_ID}`);
      }
    }
    
    // 5. Équipementiers
    console.log('Importing equipementiers...');
    const [equipementiers] = await connection.execute(
      `SELECT PM_ID, PM_NAME, PM_NAME_META, PM_LOGO, PM_PREVIEW, 
      PM_DISPLAY, PM_TOP, PM_SORT 
      FROM PIECES_MARQUE 
      WHERE PM_DISPLAY = 1`
    );
    
    console.log(`Found ${equipementiers.length} equipementiers`);
    
    // Supprimer tous les équipementiers existants
    await prisma.equipementier.deleteMany({});
    
    // Insérer les nouveaux équipementiers
    for (const equip of equipementiers) {
      await prisma.equipementier.create({
        data: {
          id: equip.PM_ID,
          name: equip.PM_NAME,
          nameMeta: equip.PM_NAME_META || equip.PM_NAME,
          logo: equip.PM_LOGO,
          preview: equip.PM_PREVIEW || '',
          display: equip.PM_DISPLAY === 1,
          top: equip.PM_TOP === 1,
          sort: equip.PM_SORT || 0
        }
      });
    }
    
    // 6. SEO des gammes
    console.log('Importing gamme SEO...');
    const [seoGammes] = await connection.execute(
      `SELECT SG_ID, SG_PG_ID, SG_TITLE, SG_DESCRIP 
      FROM __SEO_GAMME`
    );
    
    console.log(`Found ${seoGammes.length} gamme SEO entries`);
    
    // Supprimer toutes les entrées SEO existantes
    await prisma.gammeSeo.deleteMany({});
    
    // Insérer les nouvelles entrées SEO
    for (const seo of seoGammes) {
      try {
        await prisma.gammeSeo.create({
          data: {
            id: seo.SG_ID,
            gammeId: seo.SG_PG_ID,
            title: seo.SG_TITLE || '',
            description: seo.SG_DESCRIP || ''
          }
        });
      } catch (error) {
        // Ignorer les erreurs de clé étrangère pour les entrées orphelines
        console.log(`Skipped SEO for gamme ${seo.SG_PG_ID}`);
      }
    }
    
    // 7. Conseils blog
    console.log('Importing blog advice...');
    const [blogAdvices] = await connection.execute(
      `SELECT BA_ID, BA_PG_ID, BA_PREVIEW 
      FROM __BLOG_ADVICE`
    );
    
    console.log(`Found ${blogAdvices.length} blog advice entries`);
    
    // Supprimer tous les conseils blog existants
    await prisma.blogAdvice.deleteMany({});
    
    // Insérer les nouveaux conseils blog
    for (const advice of blogAdvices) {
      try {
        await prisma.blogAdvice.create({
          data: {
            id: advice.BA_ID,
            gammeId: advice.BA_PG_ID,
            preview: advice.BA_PREVIEW || ''
          }
        });
      } catch (error) {
        // Ignorer les erreurs de clé étrangère pour les entrées orphelines
        console.log(`Skipped blog advice for gamme ${advice.BA_PG_ID}`);
      }
    }
    
    console.log('Import completed successfully!');
  } catch (error) {
    console.error('Error during import:', error);
  } finally {
    await connection.end();
    await prisma.$disconnect();
  }
}

importFromMySQL()
  .then(() => {
    console.log('Import script finished');
    exit(0);
  })
  .catch((error) => {
    console.error('Fatal error:', error);
    exit(1);
  });
