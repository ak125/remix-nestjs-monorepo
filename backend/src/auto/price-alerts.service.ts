import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { Cron } from '@nestjs/schedule';

@Injectable()
export class PriceAlertsService {
  constructor(private prisma: PrismaService) {}

  async createAlert(userId: string, modelId: number, priceLimit: number) {
    return this.prisma.priceAlert.create({
      data: {
        userId,
        modelId,
        priceLimit
      }
    });
  }

  @Cron('0 */12 * * *') // Toutes les 12 heures
  async checkPriceAlerts() {
    const alerts = await this.prisma.priceAlert.findMany({
      where: { notified: false },
      include: {
        model: {
          include: {
            specifications: true
          }
        }
      }
    });

    for (const alert of alerts) {
      if (alert.model.specifications.price <= alert.priceLimit) {
        // Créer une notification
        await this.prisma.notification.create({
          data: {
            userId: alert.userId,
            modelId: alert.modelId,
            type: 'PRICE_DROP',
            message: `Le prix de ${alert.model.name} est passé sous ${alert.priceLimit}€!`
          }
        });

        // Marquer l'alerte comme notifiée
        await this.prisma.priceAlert.update({
          where: { id: alert.id },
          data: { notified: true }
        });
      }
    }
  }
}
