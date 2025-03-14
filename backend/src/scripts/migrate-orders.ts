import { PrismaClient } from '@prisma/client';
import * as dotenv from 'dotenv';

// Charger les variables d'environnement
dotenv.config();

const prisma = new PrismaClient();

async function moveOldOrdersToArchive() {
  console.log('🔄 Début de la migration des anciennes commandes...');
  
  // Date il y a un an
  const oneYearAgo = new Date();
  oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);

  try {
    // Récupérer les anciennes commandes
    const oldOrders = await prisma.xTR_ORDER.findMany({
      where: {
        order_date: {
          lt: oneYearAgo,
        },
        order_paid: true, // Uniquement les commandes payées
      },
      include: {
        xTR_ORDER_ITEMS: true,
      },
    });

    console.log(`📦 ${oldOrders.length} commandes à archiver...`);

    let successCount = 0;
    let errorCount = 0;

    // Traiter chaque commande
    for (const order of oldOrders) {
      try {
        // Utiliser une transaction pour garantir la cohérence
        await prisma.$transaction(async (tx) => {
          // Créer l'archive de la commande
          await tx.xTR_ORDER_ARCHIVE.create({
            data: {
              order_id: order.order_id,
              order_cst_id: order.order_cst_id,
              order_date: order.order_date,
              order_status: 'archived',
              order_total: order.order_total,
              order_payment_method: order.order_payment_method,
              order_payment_date: order.order_payment_date,
              order_billing_address_id: order.order_billing_address_id,
              order_delivery_address_id: order.order_delivery_address_id,
            },
          });

          // Archiver les items de la commande
          for (const item of order.xTR_ORDER_ITEMS) {
            await tx.xTR_ORDER_ARCHIVE_ITEMS.create({
              data: {
                order_id: order.order_id,
                piece_id: item.piece_id,
                quantity: item.quantity,
                price: item.price,
              },
            });
          }

          // Supprimer les items originaux
          await tx.xTR_ORDER_ITEMS.deleteMany({
            where: { order_id: order.order_id },
          });

          // Supprimer la commande originale
          await tx.xTR_ORDER.delete({
            where: { order_id: order.order_id },
          });
        });

        successCount++;
        process.stdout.write(`\r✅ Progression: ${successCount}/${oldOrders.length}`);
      } catch (error) {
        errorCount++;
        console.error(`\n❌ Erreur lors du traitement de la commande ${order.order_id}:`, error);
      }
    }

    console.log('\n\n📊 Résumé de la migration:');
    console.log(`✅ Commandes archivées avec succès: ${successCount}`);
    console.log(`❌ Échecs: ${errorCount}`);

  } catch (error) {
    console.error('🚨 Erreur fatale lors de la migration:', error);
    process.exit(1);
  } finally {
    await prisma.$disconnect();
  }
}

// Exécuter la migration
moveOldOrdersToArchive().catch(console.error);
