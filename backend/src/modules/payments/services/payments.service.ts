import { Injectable, Logger, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { EmailService } from '../../shared/mailer/email.service';
import * as crypto from 'crypto';

type PaymentData = {
  signature: string;
  vads_order_id: string;
  vads_result: string;
  vads_amount: number;
  vads_card_brand: string;
  vads_card_country: string;
  vads_trans_id: string;
};

@Injectable()
export class PaymentsService {
  private readonly logger = new Logger(PaymentsService.name);
  private readonly certificatProd = process.env.PAYMENT_CERTIFICATE;

  constructor(
    private readonly prisma: PrismaService,
    private readonly eventEmitter: EventEmitter2,
    private readonly emailService: EmailService,
  ) {}

  async processPayment(paymentData: PaymentData) {
    try {
      // Vérification signature
      if (!this.verifySignature(paymentData)) {
        throw new BadRequestException('Signature invalide');
      }

      const isSuccess = paymentData.vads_result === '00';
      const orderId = this.formatOrderId(paymentData.vads_order_id);

      // Transaction atomique
      const [payment, order] = await this.prisma.$transaction([
        // Log paiement
        this.prisma.payment.create({
          data: {
            orderId,
            transactionId: paymentData.vads_trans_id,
            amount: paymentData.vads_amount / 100,
            status: isSuccess ? 'PAID' : 'FAILED',
            method: paymentData.vads_card_brand,
            countryCode: paymentData.vads_card_country,
            rawData: paymentData,
          },
        }),

        // Mise à jour commande
        this.prisma.order.update({
          where: { id: orderId },
          data: {
            status: isSuccess ? 'PAID' : 'PAYMENT_FAILED',
            isPaid: isSuccess,
            paymentDate: isSuccess ? new Date() : null,
          },
          include: {
            customer: true,
          },
        }),
      ]);

      // Notifications
      if (isSuccess) {
        await this.notifyPaymentSuccess(order);
      } else {
        await this.notifyPaymentFailure(order);
      }

      return {
        success: isSuccess,
        orderId,
        payment,
      };

    } catch (error) {
      this.logger.error('Erreur traitement paiement', {
        error,
        orderId: paymentData.vads_order_id,
      });
      throw error;
    }
  }

  async verifyWebhookSignature(payload: unknown, signature: string): Promise<any> {
    const computedSignature = crypto
      .createHmac('sha256', this.certificatProd)
      .update(JSON.stringify(payload))
      .digest('hex');

    if (computedSignature !== signature) {
      throw new BadRequestException('Signature webhook invalide');
    }

    return payload;
  }

  async processWebhook(payload: any) {
    try {
      const orderId = this.formatOrderId(payload.vads_order_id);
      const isSuccess = payload.vads_result === '00';

      // Transaction atomique
      const [payment, order] = await this.prisma.$transaction([
        // Update paiement
        this.prisma.payment.update({
          where: { orderId },
          data: {
            status: isSuccess ? 'PAID' : 'FAILED',
            webhookData: payload,
            updatedAt: new Date(),
          },
        }),

        // Update commande
        this.prisma.order.update({
          where: { id: orderId },
          data: {
            status: isSuccess ? 'PAID' : 'PAYMENT_FAILED',
            isPaid: isSuccess,
          },
          include: {
            customer: true,
          },
        }),
      ]);

      // Notifications
      if (isSuccess) {
        await this.notifyPaymentSuccess(order);
      } else {
        await this.notifyPaymentFailure(order);
      }

      return { success: true };

    } catch (error) {
      this.logger.error('Erreur traitement webhook', {
        error,
        payload,
      });
      throw error;
    }
  }

  private verifySignature(data: PaymentData): boolean {
    const signature = this.generateSignature(data);
    return signature === data.signature;
  }

  private generateSignature(data: PaymentData): string {
    const fields = Object.entries(data)
      .filter(([key]) => key.startsWith('vads_'))
      .sort(([a], [b]) => a.localeCompare(b))
      .map(([, value]) => value)
      .join('+');

    return crypto
      .createHash('sha256')
      .update(`${fields}+${this.certificatProd}`)
      .digest('hex');
  }

  private formatOrderId(orderId: string): string {
    return orderId.replace('-', '/');
  }

  private async notifyPaymentSuccess(order: any) {
    this.eventEmitter.emit('payment.success', {
      orderId: order.id,
      customerId: order.customer.id,
    });

    await this.emailService.sendPaymentSuccessEmail({
      to: order.customer.email,
      orderId: order.id,
    });
  }

  private async notifyPaymentFailure(order: any) {
    this.eventEmitter.emit('payment.failed', {
      orderId: order.id,
      customerId: order.customer.id,
    });
  }
}
