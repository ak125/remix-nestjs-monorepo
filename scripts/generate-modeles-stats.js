const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

/**
 * Ce script génère des statistiques sur les modèles de voitures
 * pour le tableau de bord administratif.
 */
async function generateModeleStats() {
  try {
    console.log('Generating modele statistics...');
    
    // Nombre total de modèles
    const totalModeles = await prisma.modele.count();
    
    // Nombre de modèles actifs
    const activeModeles = await prisma.modele.count({
      where: { display: true }
    });
    
    // Modèles par marque
    const modelesByMarque = await prisma.marque.findMany({
      where: { display: true },
      select: {
        id: true,
        name: true,
        _count: {
          select: { modeles: true }
        }
      },
      orderBy: {
        modeles: {
          _count: 'desc'
        }
      },
      take: 10
    });
    
    // Modèles par décennie
    const currentYear = new Date().getFullYear();
    const decades = [];
    
    for (let decade = 1900; decade <= Math.floor(currentYear / 10) * 10; decade += 10) {
      const count = await prisma.modele.count({
        where: {
          yearFrom: {
            gte: decade,
            lt: decade + 10
          }
        }
      });
      
      decades.push({
        decade: `${decade}-${decade + 9}`,
        count
      });
    }
    
    // Modèles récemment ajoutés ou modifiés
    const recentModeles = await prisma.modele.findMany({
      orderBy: { updatedAt: 'desc' },
      take: 5,
      include: {
        marque: {
          select: {
            name: true
          }
        }
      }
    });
    
    // Statistiques globales
    const stats = {
      total: totalModeles,
      active: activeModeles,
      inactive: totalModeles - activeModeles,
      activePercentage: Math.round((activeModeles / totalModeles) * 100),
      byMarque: modelesByMarque,
      byDecade: decades,
      recent: recentModeles.map(m => ({
        id: m.id,
        name: m.name,
        marque: m.marque.name,
        updatedAt: m.updatedAt
      }))
    };
    
    console.log('Statistics generated:');
    console.log('- Total modeles:', stats.total);
    console.log('- Active modeles:', stats.active);
    console.log('- Inactive modeles:', stats.inactive);
    console.log('- Most popular marque:', stats.byMarque[0]?.name);
    
    // Écrire dans un fichier ou une base de données si nécessaire
    // await fs.writeFile('./modeles-stats.json', JSON.stringify(stats, null, 2));
    
    // Vous pourriez aussi stocker ces statistiques dans Redis ou une autre table
    
    return stats;
  } catch (error) {
    console.error('Error generating modele statistics:', error);
    throw error;
  } finally {
    await prisma.$disconnect();
  }
}

// Exécuter le script si appelé directement
if (require.main === module) {
  generateModeleStats()
    .then(stats => {
      console.log('Stats generation completed successfully!');
      process.exit(0);
    })
    .catch(err => {
      console.error('Error running stats generation script:', err);
      process.exit(1);
    });
}

module.exports = { generateModeleStats };
