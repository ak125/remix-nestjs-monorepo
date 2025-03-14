import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { TrackingService } from './tracking.service';
import { NotificationService } from '../notification/notification.service';
import { z } from 'zod';

const deliverySchema = z.object({
  orderId: z.string(),
  trackingNumber: z.string(),
  carrier: z.string(),
  estimatedDate: z.string().transform(str => new Date(str))
});

@Injectable()
export class DeliveryService {
  constructor(
    private prisma: PrismaService,
    private tracking: TrackingService,
    private notification: NotificationService
  ) {}

  async createDelivery(data: unknown) {
    const validated = deliverySchema.parse(data);

    const delivery = await this.prisma.delivery.create({
      data: validated,
      include: {
        order: {
          select: {
            customerEmail: true
          }
        }
      }
    });

    await this.notification.sendDeliveryCreated(
      delivery.order.customerEmail,
      delivery
    );

    return delivery;
  }

  async updateDeliveryStatus(id: string, status: string) {
    const delivery = await this.prisma.delivery.update({
      where: { id },
      data: { 
        status,
        events: {
          create: {
            status,
            message: `Statut mis à jour: ${status}`
          }
        }
      },
      include: {
        order: {
          select: {
            customerEmail: true
          }
        }
      }
    });

    await this.notification.sendDeliveryUpdate(
      delivery.order.customerEmail,
      delivery
    );

    return delivery;
  }

  async getDeliveryEvents(id: string) {
    const delivery = await this.prisma.delivery.findUnique({
      where: { id },
      include: {
        events: {
          orderBy: { timestamp: 'desc' }
        }
      }
    });

    if (!delivery) {
      throw new NotFoundException(`Delivery ${id} not found`);
    }

    return delivery;
  }

  async syncTrackingStatus(id: string) {
    const delivery = await this.prisma.delivery.findUnique({
      where: { id }
    });

    if (!delivery) {
      throw new NotFoundException(`Delivery ${id} not found`);
    }

    const trackingInfo = await this.tracking.trackPackage(
      delivery.carrier,
      delivery.trackingNumber
    );

    await this.updateDeliveryStatus(id, trackingInfo.status);

    return trackingInfo;
  }
}
