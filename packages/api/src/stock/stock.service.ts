import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { NotificationService } from '../notification/notification.service';
import { z } from 'zod';

const updateStockSchema = z.object({
  quantity: z.number().min(0),
  reason: z.string().optional(),
  userId: z.string()
});

@Injectable()
export class StockService {
  constructor(
    private prisma: PrismaService,
    private notification: NotificationService
  ) {}

  async updateStock(id: string, data: unknown) {
    const validated = updateStockSchema.parse(data);
    const stock = await this.getStock(id);

    const updated = await this.prisma.stock.update({
      where: { id },
      data: {
        quantity: validated.quantity,
        history: {
          create: {
            oldQuantity: stock.quantity,
            newQuantity: validated.quantity,
            reason: validated.reason,
            userId: validated.userId
          }
        }
      }
    });

    // Check stock levels
    if (updated.quantity <= updated.minQuantity) {
      await this.createStockAlert(id);
      await this.notification.sendLowStockAlert(updated);
    }

    return updated;
  }

  async disableStock(id: string, userId: string) {
    return this.prisma.$transaction(async tx => {
      const stock = await this.getStock(id);

      await tx.stock.update({
        where: { id },
        data: { 
          isActive: false,
          history: {
            create: {
              oldQuantity: stock.quantity,
              newQuantity: 0,
              reason: 'Stock désactivé',
              userId
            }
          }
        }
      });
    });
  }

  async getLowStockItems() {
    return this.prisma.stock.findMany({
      where: {
        isActive: true,
        quantity: {
          lte: this.prisma.stock.fields.minQuantity
        }
      },
      include: {
        alerts: {
          where: {
            resolved: false
          }
        }
      }
    });
  }

  private async createStockAlert(stockId: string) {
    return this.prisma.stockAlert.create({
      data: {
        stockId,
        type: 'low_stock',
        message: 'Stock en dessous du seuil minimum'
      }
    });
  }

  private async getStock(id: string) {
    const stock = await this.prisma.stock.findUnique({
      where: { id }
    });

    if (!stock) {
      throw new NotFoundException(`Stock ${id} not found`);
    }

    return stock;
  }
}
