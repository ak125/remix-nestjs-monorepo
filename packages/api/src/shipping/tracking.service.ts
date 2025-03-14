import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { NotificationsService } from "../notifications/notifications.service";
import * as Colissimo from "@colissimo/api";
import * as Chronopost from "@chronopost/api";
import * as DHL from "@dhl/api";
import { z } from "zod";

const trackingSchema = z.object({
  trackingNumber: z.string(),
  carrier: z.enum(['colissimo', 'chronopost', 'dhl']),
  zipCode: z.string().optional()
});

@Injectable()
export class TrackingService {
  private carriers: Map<string, any>;

  constructor(
    private prisma: PrismaService,
    private notifications: NotificationsService
  ) {
    this.carriers = new Map([
      ['colissimo', new Colissimo(process.env.COLISSIMO_API_KEY!)],
      ['chronopost', new Chronopost(process.env.CHRONOPOST_API_KEY!)],
      ['dhl', new DHL(process.env.DHL_API_KEY!)]
    ]);
  }

  async getTrackingInfo(data: unknown) {
    const { trackingNumber, carrier } = trackingSchema.parse(data);
    const carrierApi = this.carriers.get(carrier);

    const tracking = await carrierApi.getTracking(trackingNumber);
    
    await this.prisma.tracking.create({
      data: {
        trackingNumber,
        carrier,
        status: tracking.status,
        estimatedDelivery: tracking.estimatedDelivery,
        lastUpdate: new Date()
      }
    });

    return {
      status: tracking.status,
      estimatedDelivery: tracking.estimatedDelivery,
      history: tracking.events
    };
  }

  async getEstimatedDelivery(zipCode: string, carrier: string) {
    const response = await fetch(
      `https://api.${carrier}.com/estimate?zip=${zipCode}`, 
      {
        headers: {
          'Authorization': `Bearer ${process.env[`${carrier.toUpperCase()}_API_KEY`]}`
        }
      }
    );

    if (!response.ok) {
      throw new Error(`Erreur API ${carrier}`);
    }

    return response.json();
  }

  async checkDeliveryStatus() {
    const pendingDeliveries = await this.prisma.tracking.findMany({
      where: {
        status: { not: 'delivered' }
      }
    });

    for (const delivery of pendingDeliveries) {
      const tracking = await this.getTrackingInfo({
        trackingNumber: delivery.trackingNumber,
        carrier: delivery.carrier
      });

      if (tracking.status !== delivery.status) {
        await this.notifications.sendDeliveryUpdate({
          trackingNumber: delivery.trackingNumber,
          status: tracking.status,
          estimatedDelivery: tracking.estimatedDelivery
        });
      }
    }
  }
}
