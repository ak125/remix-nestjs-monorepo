import { prisma } from "~/lib/db.server";

interface VIPBenefit {
  type: 'shipping' | 'cashback' | 'early_access';
  value: number;
  description: string;
}

export class VIPService {
  private readonly benefits: VIPBenefit[] = [
    { type: 'shipping', value: 100, description: 'Livraison gratuite illimitée' },
    { type: 'cashback', value: 5, description: '5% de cashback sur chaque achat' },
    { type: 'early_access', value: 24, description: 'Accès 24h en avance aux promotions' }
  ];

  async getVIPStatus(userId: string) {
    const subscription = await prisma.subscription.findFirst({
      where: {
        userId,
        status: 'active',
        type: 'VIP'
      }
    });

    return {
      isVIP: !!subscription,
      benefits: subscription ? this.benefits : [],
      subscription
    };
  }

  async createSubscription(userId: string) {
    return prisma.$transaction(async (tx) => {
      const subscription = await tx.subscription.create({
        data: {
          userId,
          type: 'VIP',
          status: 'active',
          price: 9.99,
          benefits: this.benefits
        }
      });

      await tx.notification.create({
        data: {
          userId,
          type: 'subscription_created',
          title: 'Bienvenue dans le programme VIP !',
          message: 'Profitez dès maintenant de vos avantages exclusifs.'
        }
      });

      return subscription;
    });
  }
}
