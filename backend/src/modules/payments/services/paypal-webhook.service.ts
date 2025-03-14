import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { AdminNotificationsService } from '../../admin/services/notifications.service';
import { PayPalConfigService } from '../config/paypal.config';

@Injectable()
export class PayPalWebhookService {
  private readonly logger = new Logger(PayPalWebhookService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly paypalConfig: PayPalConfigService,
    private readonly notifications: AdminNotificationsService,
  ) {}

  async handleWebhook(
    event: any,
    headers: Record<string, string>,
  ) {
    try {
      // Vérifier la signature webhook
      await this.paypalConfig.verifyWebhookSignature(event, headers);

      switch (event.event_type) {
        case 'PAYMENT.AUTHORIZATION.VOIDED':
        case 'PAYMENT.CAPTURE.DENIED':
          await this.handlePaymentCancelled(event);
          break;
        // ...autres cas
      }

    } catch (error) {
      this.logger.error('Erreur webhook PayPal', { error, event });
      throw error;
    }
  }

  private async handlePaymentCancelled(event: any) {
    const orderId = event.resource.custom_id;
    const reason = event.resource.status_details?.reason || 'Paiement annulé';

    await this.prisma.$transaction([
      // Mise à jour commande
      this.prisma.order.update({
        where: { id: orderId },
        data: {
          status: 'CANCELLED',
          cancelReason: reason,
          cancelledAt: new Date(),
        },
      }),

      // Notification admin
      this.notifications.createNotification({
        type: 'PAYMENT_CANCELLED',
        title: 'Paiement PayPal annulé',
        message: `La commande ${orderId} a été annulée : ${reason}`,
        metadata: {
          orderId,
          reason,
          paypalEvent: event.event_type,
        },
      }),
    ]);
  }
}
