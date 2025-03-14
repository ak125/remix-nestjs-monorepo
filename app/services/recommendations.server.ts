import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

interface RecommendationOptions {
  limit?: number;
  excludeIds?: string[];
  categories?: string[];
}

export class RecommendationService {
  async getRecommendations(cartItems: Array<{ id: string; categoryId: string }>, options: RecommendationOptions = {}) {
    const { limit = 4, excludeIds = [] } = options;
    const categoryIds = [...new Set(cartItems.map(item => item.categoryId))];
    const itemIds = [...new Set([...cartItems.map(item => item.id), ...excludeIds])];

    const cacheKey = `recommendations:${categoryIds.join(',')}`;
    const cached = await cache.get(cacheKey);
    if (cached) return cached;

    const recommendations = await prisma.product.findMany({
      where: {
        categoryId: { in: categoryIds },
        id: { notIn: itemIds },
        isActive: true,
        stock: { gt: 0 }
      },
      include: {
        category: true,
        brand: true,
        _count: {
          select: { orders: true }
        }
      },
      orderBy: {
        orders: { _count: 'desc' }
      },
      take: limit
    });

    await cache.set(cacheKey, recommendations, 60 * 15); // 15 minutes
    return recommendations;
  }
}
