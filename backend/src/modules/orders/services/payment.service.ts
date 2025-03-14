import { Injectable, BadRequestException, Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { EmailService } from '../../shared/mailer/email.service';
import * as crypto from 'crypto';

interface PaymentData {
  vads_order_id: string;
  vads_auth_number: string;
  vads_amount: number;
  vads_card_brand: string;
  vads_card_country: string;
  vads_trans_id: string;
  vads_result: string;
  signature: string;
}

@Injectable()
export class PaymentService {
  private readonly CERTIFICATE_PROD = process.env.PAYMENT_CERTIFICATE;
  private readonly logger = new Logger(PaymentService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly eventEmitter: EventEmitter2,
    private readonly emailService: EmailService,
  ) {}

  async processPayment(paymentData: PaymentData, sessionUser: any) {
    try {
      // Validation utilisateur
      if (!sessionUser?.email) {
        throw new BadRequestException('Utilisateur non authentifié');
      }

      // Validation signature
      if (!this.validateSignature(paymentData)) {
        this.logger.error('Signature invalide', 'Payment', { 
          orderId: paymentData.vads_order_id 
        });
        throw new BadRequestException('Signature invalide');
      }

      const orderId = this.extractOrderId(paymentData.vads_order_id);

      if (paymentData.vads_result === '00') {
        // Transaction atomic
        const [order, paymentRecord] = await this.prisma.$transaction([
          // Mise à jour commande
          this.prisma.order.update({
            where: { id: orderId },
            data: {
              status: 'PAID',
              isPaid: true,
              paymentDate: new Date(),
              paymentDetails: {
                create: {
                  authNumber: paymentData.vads_auth_number,
                  transactionId: paymentData.vads_trans_id,
                  method: paymentData.vads_card_brand,
                  country: paymentData.vads_card_country,
                  amount: paymentData.vads_amount / 100,
                },
              },
            },
            include: {
              customer: true,
              orderLines: true,
            },
          }),

          // Log paiement
          this.prisma.paymentLog.create({
            data: {
              orderId,
              amount: paymentData.vads_amount / 100,
              status: 'SUCCESS',
              transactionId: paymentData.vads_trans_id,
              rawData: JSON.stringify(paymentData),
            },
          }),
        ]);

        // Events & notifications
        await this.handleSuccessfulPayment(order);

        return { 
          success: true,
          order,
          payment: paymentRecord,
        };

      } else {
        await this.handleFailedPayment(orderId, paymentData);
        throw new BadRequestException('Paiement refusé');
      }

    } catch (error) {
      this.logger.error('Erreur traitement paiement', 'Payment', {
        error,
        orderId: paymentData.vads_order_id,
      });
      throw error;
    }
  }

  private validateSignature(data: PaymentData): boolean {
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
      .update(`${fields}+${this.CERTIFICATE_PROD}`)
      .digest('hex');
  }

  private async handleSuccessfulPayment(order: any) {
    // Notification WebSocket
    this.eventEmitter.emit('payment.success', {
      orderId: order.id,
      customerId: order.customer.id,
    });

    // Email confirmation
    await this.emailService.sendPaymentConfirmation({
      to: order.customer.email,
      order: {
        id: order.id,
        amount: order.totalAmount,
        items: order.orderLines,
      },
    });

    this.logger.info('Paiement traité avec succès', 'Payment', {
      orderId: order.id,
    });
  }

  private async handleFailedPayment(orderId: number, data: PaymentData) {
    await this.prisma.paymentLog.create({
      data: {
        orderId,
        status: 'FAILED',
        amount: data.vads_amount / 100,
        transactionId: data.vads_trans_id,
        rawData: JSON.stringify(data),
      },
    });

    this.logger.error('Paiement échoué', 'Payment', {
      orderId,
      result: data.vads_result,
    });
  }

  private extractOrderId(orderRef: string): number {
    const id = parseInt(orderRef.split('/')[0], 10);
    if (isNaN(id)) {
      throw new BadRequestException('Référence commande invalide');
    }
    return id;
  }

  async handleFailedPayment(orderId: string, error: string, errorCode?: string) {
    try {
      // Transaction pour cohérence des données
      const [paymentLog, order] = await this.prisma.$transaction([
        // Log de l'échec
        this.prisma.paymentLog.create({
          data: {
            orderId: this.formatOrderId(orderId),
            status: 'FAILED',
            errorMessage: error,
            errorCode,
          },
        }),

        // Mise à jour commande
        this.prisma.order.update({
          where: { id: this.formatOrderId(orderId) },
          data: { 
            status: 'PAYMENT_FAILED',
            lastError: error,
          },
          include: {
            customer: true,
          },
        }),
      ]);

      // Notifications
      await this.notifyPaymentFailure(order, error);

      return {
        success: false,
        message: 'Paiement échoué',
        orderId: this.formatOrderId(orderId),
        error,
      };

    } catch (error) {
      this.logger.error('Erreur traitement échec paiement', {
        error,
        orderId,
      });
      throw error;
    }
  }

  private async notifyPaymentFailure(order: any, error: string) {
    // Event pour WebSocket
    this.eventEmitter.emit('payment.failed', {
      orderId: order.id,
      customerId: order.customer.id,
    });

    // Email client
    await this.emailService.sendPaymentFailedEmail({
      to: order.customer.email,
      orderId: order.id,
      error,
    });
  }

  private formatOrderId(orderId: string): string {
    return orderId.replace('-', '/');
  }
}
