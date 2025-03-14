import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { NotificationsService } from "../notifications/notifications.service";
import * as Colissimo from "@colissimo/api";
import * as DHL from "@dhl/api";
import { z } from "zod";

const shippingSchema = z.object({
  orderId: z.string(),
  carrier: z.enum(['colissimo', 'dhl', 'fedex']),
  recipientInfo: z.object({
    name: z.string(),
    address: z.string(),
    zipCode: z.string(),
    city: z.string(),
    country: z.string(),
    phone: z.string().optional(),
    email: z.string().email()
  })
});

@Injectable()
export class ShippingService {
  private carriers: Map<string, any>;

  constructor(
    private prisma: PrismaService,
    private notifications: NotificationsService
  ) {
    this.carriers = new Map([
      ['colissimo', new Colissimo(process.env.COLISSIMO_API_KEY!)],
      ['dhl', new DHL(process.env.DHL_API_KEY!)]
    ]);
  }

  async createShipment(data: unknown) {
    const validated = shippingSchema.parse(data);
    const carrier = this.carriers.get(validated.carrier);

    // Créer l'étiquette
    const label = await carrier.createShipment({
      recipient: validated.recipientInfo,
      format: 'pdf'
    });

    // Enregistrer dans la base de données
    const shipment = await this.prisma.shipment.create({
      data: {
        orderId: validated.orderId,
        carrier: validated.carrier,
        trackingNumber: label.trackingNumber,
        status: 'created',
        recipientInfo: validated.recipientInfo
      }
    });

    // Notifier le client
    await this.notifications.sendShippingUpdate({
      type: 'shipment_created',
      trackingNumber: label.trackingNumber,
      email: validated.recipientInfo.email,
      phone: validated.recipientInfo.phone
    });

    return {
      shipmentId: shipment.id,
      trackingNumber: label.trackingNumber,
      labelUrl: label.url
    };
  }

  async getShipmentStatus(trackingNumber: string) {
    const shipment = await this.prisma.shipment.findFirst({
      where: { trackingNumber }
    });

    if (!shipment) {
      throw new Error("Expédition non trouvée");
    }

    const carrier = this.carriers.get(shipment.carrier);
    const status = await carrier.getTracking(trackingNumber);

    // Mettre à jour le statut
    await this.prisma.shipment.update({
      where: { id: shipment.id },
      data: { 
        status: status.code,
        lastUpdate: status.timestamp
      }
    });

    if (status.hasChanged) {
      await this.notifications.sendShippingUpdate({
        type: 'status_updated',
        trackingNumber,
        status: status.code,
        email: shipment.recipientInfo.email,
        phone: shipment.recipientInfo.phone
      });
    }

    return status;
  }

  async getShippingStats(startDate: string, endDate: string) {
    const [dailyStats, carrierStats] = await Promise.all([
      this.prisma.shipment.groupBy({
        by: ['createdAt'],
        where: {
          createdAt: {
            gte: new Date(startDate),
            lte: new Date(endDate)
          }
        },
        _count: true
      }),
      this.prisma.shipment.groupBy({
        by: ['carrier'],
        _count: true
      })
    ]);

    return {
      daily: dailyStats,
      byCarrier: carrierStats
    };
  }
}
