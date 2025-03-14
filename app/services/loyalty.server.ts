import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

interface LoyaltyPoints {
  points: number;
  value: number;
  nextReward: {
    points: number;
    value: number;
  };
}

export class LoyaltyService {
  private readonly POINTS_PER_EURO = 1;
  private readonly POINTS_VALUE = 0.05; // 1 point = 0.05€

  async getLoyaltyPoints(userId: string): Promise<LoyaltyPoints> {
    const cacheKey = `loyalty:${userId}`;
    const cached = await cache.get(cacheKey);
    if (cached) return cached;

    const loyalty = await prisma.loyalty.findUnique({
      where: { userId },
      select: { points: true }
    });

    const points = loyalty?.points || 0;
    const value = Math.floor(points * this.POINTS_VALUE);
    const nextReward = {
      points: Math.ceil((value + 5) / this.POINTS_VALUE),
      value: value + 5
    };

    const result = { points, value, nextReward };
    await cache.set(cacheKey, result, 60 * 5); // 5 minutes
    
    return result;
  }

  async addPoints(userId: string, amount: number) {
    const points = Math.floor(amount * this.POINTS_PER_EURO);
    
    return prisma.loyalty.upsert({
      where: { userId },
      create: { userId, points },
      update: { points: { increment: points } }
    });
  }

  async redeemPoints(userId: string, points: number) {
    const loyalty = await prisma.loyalty.findUnique({ 
      where: { userId } 
    });

    if (!loyalty || loyalty.points < points) {
      throw new Error('Points insuffisants');
    }

    return prisma.$transaction([
      prisma.loyalty.update({
        where: { userId },
        data: { points: { decrement: points } }
      }),
      prisma.loyaltyHistory.create({
        data: {
          userId,
          type: 'REDEEM',
          points: -points,
          value: points * this.POINTS_VALUE
        }
      })
    ]);
  }
}
