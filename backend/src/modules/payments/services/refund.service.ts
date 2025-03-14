import { Injectable, Logger, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { PayPalConfigService } from '../config/paypal.config';
import { EventEmitter2 } from '@nestjs/event-emitter';
import * as paypal from '@paypal/checkout-server-sdk';

@Injectable()
export class RefundService {
  private readonly logger = new Logger(RefundService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly paypalConfig: PayPalConfigService,
    private readonly eventEmitter: EventEmitter2,
  ) {}

  async refundPayment(orderId: string, amount?: number) {
    try {
      const payment = await this.prisma.payment.findFirst({
        where: { orderId },
        include: { order: true },
      });

      if (!payment?.providerTransactionId) {
        throw new BadRequestException('Transaction introuvable');
      }

      // Création refund PayPal
      const request = new paypal.payments.CapturesRefundRequest(payment.providerTransactionId);
      
      if (amount) {
        request.requestBody({
          amount: {
            value: amount.toFixed(2),
            currency_code: 'EUR',
          },
        });
      }

      const client = this.paypalConfig.getClient();
      const response = await client.execute(request);

      // Transaction mise à jour
      const [refund, updatedPayment] = await this.prisma.$transaction([
        // Log remboursement
        this.prisma.refund.create({
          data: {
            paymentId: payment.id,
            amount: amount || payment.amount,
            refundId: response.result.id,
            status: 'COMPLETED',
          },
        }),

        // Update paiement
        this.prisma.payment.update({
          where: { id: payment.id },
          data: {
            status: 'REFUNDED',
            refundDate: new Date(),
          },
        }),
      ]);

      // Notification
      this.eventEmitter.emit('payment.refunded', {
        orderId,
        refundId: refund.id,
        amount: refund.amount,
      });

      return { refund, payment: updatedPayment };

    } catch (error) {
      this.logger.error('Erreur remboursement', { error, orderId });
      throw error;
    }
  }
}
