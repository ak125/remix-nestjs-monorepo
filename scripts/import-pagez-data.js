const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function importPageZData() {
  // Cette fonction simule l'importation des données depuis la base MySQL originale
  // Dans un cas réel, vous devriez utiliser une connexion directe à la base de données source
  
  try {
    // Exemple de données à importer
    const pagesToImport = [
      { mfId: 1, pgId: 101, pgName: "Amortisseurs", mfName: "Suspension", 
        seoTitle: "Amortisseurs de haute qualité", seoDesc: "Découvrez notre gamme d'amortisseurs" },
      { mfId: 1, pgId: 102, pgName: "Ressorts", mfName: "Suspension", 
        seoTitle: "Ressorts pour tous véhicules", seoDesc: "Large sélection de ressorts" },
      { mfId: 2, pgId: 201, pgName: "Filtres à huile", mfName: "Filtration", 
        seoTitle: "Filtres à huile pour votre voiture", seoDesc: "Filtres à huile de qualité" },
      { mfId: 2, pgId: 202, pgName: "Filtres à air", mfName: "Filtration", 
        seoTitle: "Filtres à air pour moteurs", seoDesc: "Optimisez les performances de votre moteur" },
      { mfId: 3, pgId: 301, pgName: "Freins à disque", mfName: "Freinage", 
        seoTitle: "Freins à disque haute performance", seoDesc: "Sécurité maximale avec nos freins à disque" },
    ];
    
    console.log(`Importing ${pagesToImport.length} PageZ entries...`);
    
    // Supprimer les données existantes pour éviter les doublons
    await prisma.pageZ.deleteMany({});
    
    // Insérer les nouvelles données
    const createdPages = await prisma.pageZ.createMany({
      data: pagesToImport,
      skipDuplicates: true,
    });
    
    console.log(`Successfully imported ${createdPages.count} PageZ entries`);
  } catch (error) {
    console.error("Error importing PageZ data:", error);
  } finally {
    await prisma.$disconnect();
  }
}

importPageZData();
