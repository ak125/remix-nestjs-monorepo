import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";
import { createEmbedding } from "~/lib/ai.server";

export class AIRecommendationsService {
  async getPersonalizedRecommendations(userId: string) {
    const cacheKey = `recommendations:${userId}`;
    const cached = await cache.get(cacheKey);
    if (cached) return cached;

    // Récupérer l'historique d'achat et de navigation
    const [orders, viewHistory] = await Promise.all([
      prisma.order.findMany({
        where: { userId },
        include: { items: true }
      }),
      prisma.productView.findMany({
        where: { userId },
        orderBy: { createdAt: 'desc' },
        take: 50
      })
    ]);

    // Créer un embedding des préférences
    const userProfile = await createEmbedding({
      purchases: orders.flatMap(o => o.items.map(i => i.productId)),
      views: viewHistory.map(v => v.productId)
    });

    // Trouver les produits similaires
    const recommendations = await prisma.product.findMany({
      where: {
        isActive: true,
        stock: { gt: 0 },
        NOT: {
          id: {
            in: [...orders.flatMap(o => o.items.map(i => i.productId))]
          }
        }
      },
      orderBy: {
        embedding: {
          similarity: userProfile
        }
      },
      take: 10
    });

    await cache.set(cacheKey, recommendations, 60 * 15); // 15 minutes
    return recommendations;
  }
}
