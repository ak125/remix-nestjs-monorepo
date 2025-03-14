const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function seedMarkers() {
  // Définition des marqueurs statiques
  const priceLowMarkers = [
    'pas cher', 'prix bas', 'moins cher', 'prix réduit', 'meilleur prix',
    'prix mini', 'bas prix', 'mini prix', 'qualité prix', 'meilleur tarif',
    'bas tarif', 'tarif mini', 'mini tarif', 'super prix', 'prix super',
    'coût réduit', 'tarif réduit', 'bas coût', 'mini coût', 'coût mini',
    'bon prix', 'bon tarif', 'juste prix', 'tarif juste', 'petit prix',
    'petit tarif'
  ];

  const offersMarkers = [
    'vous propose', 'vous offre', 'met à votre disposition', 'vend en ligne',
    'propose en ligne', 'offre en ligne', 'met en ligne', 'propose à ces clients',
    'offre à ces clients', 'propose aux acheteurs', 'offre aux acheteurs',
    'vend à ces clients', 'propose à ces clients'
  ];

  const compatibilityMarkers = [
    'soit identique avec votre voiture', 'soit compatible avec ceux déposées',
    'soit identiques à ceux démontées', 'soit compatible avec votre véhicule'
  ];

  // Supprime les marqueurs existants
  await prisma.staticMarker.deleteMany({});

  // Insertion des marqueurs de prix bas
  await Promise.all(
    priceLowMarkers.map((value, index) => 
      prisma.staticMarker.create({
        data: {
          type: 'PRICE_LOW',
          value,
          position: index,
          active: true
        }
      })
    )
  );

  // Insertion des marqueurs d'offres
  await Promise.all(
    offersMarkers.map((value, index) => 
      prisma.staticMarker.create({
        data: {
          type: 'OFFERS',
          value,
          position: index,
          active: true
        }
      })
    )
  );

  // Insertion des marqueurs de compatibilité
  await Promise.all(
    compatibilityMarkers.map((value, index) => 
      prisma.staticMarker.create({
        data: {
          type: 'COMPATIBILITY',
          value,
          position: index,
          active: true
        }
      })
    )
  );

  console.log('Marqueurs statiques importés avec succès !');
}

seedMarkers()
  .catch(e => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
