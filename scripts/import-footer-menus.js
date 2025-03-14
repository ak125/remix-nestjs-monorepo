const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function importFooterMenus() {
  // Exemple de menus à importer
  const footerMenus = [
    // Politiques (level 1)
    { title: 'Mentions légales', alias: 'mentions-legales', level: 1, relFollow: false, position: 1 },
    { title: 'CGV', alias: 'conditions-generales-vente', level: 1, relFollow: false, position: 2 },
    { title: 'Protection des données', alias: 'protection-donnees', level: 1, relFollow: false, position: 3 },
    { title: 'Cookies', alias: 'cookies', level: 1, relFollow: false, position: 4 },
    
    // Support (level 2)
    { title: 'FAQ', alias: 'faq', level: 2, relFollow: true, position: 1 },
    { title: 'Contact', alias: 'contact', level: 2, relFollow: true, position: 2 },
    { title: 'Livraison', alias: 'livraison', level: 2, relFollow: false, position: 3 },
    { title: 'Remboursement', alias: 'remboursement', level: 2, relFollow: false, position: 4 },
  ];

  // Supprimer les menus existants (optionnel)
  await prisma.footerMenu.deleteMany({});
  
  // Insérer les nouveaux menus
  await Promise.all(
    footerMenus.map(menu => 
      prisma.footerMenu.create({
        data: {
          title: menu.title,
          alias: menu.alias,
          level: menu.level,
          relFollow: menu.relFollow,
          position: menu.position,
          active: true
        }
      })
    )
  );

  console.log(`Imported ${footerMenus.length} footer menus successfully!`);
}

importFooterMenus()
  .catch(e => {
    console.error('Error importing footer menus:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
