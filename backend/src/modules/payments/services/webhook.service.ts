import { Injectable, Logger, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { PayPalConfigService } from '../config/paypal.config';
import { EventEmitter2 } from '@nestjs/event-emitter';
import * as crypto from 'crypto';

@Injectable()
export class WebhookService {
  private readonly logger = new Logger(WebhookService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly paypalConfig: PayPalConfigService,
    private readonly eventEmitter: EventEmitter2,
  ) {}

  async verifyWebhook(
    payload: unknown,
    transmissionId: string,
    signature: string,
  ) {
    const webhookId = this.paypalConfig.getWebhookId();
    const paypalPublicKey = await this.paypalConfig.getWebhookPublicKey();

    const verifyData = this.generateVerificationData(
      webhookId,
      transmissionId,
      payload,
    );

    if (!this.verifySignature(verifyData, signature, paypalPublicKey)) {
      throw new BadRequestException('Invalid webhook signature');
    }
  }

  async handleRefund(resource: any) {
    try {
      const payment = await this.prisma.payment.findFirst({
        where: { providerTransactionId: resource.id },
        include: { order: true },
      });

      if (!payment) {
        throw new BadRequestException('Payment not found');
      }

      const refundAmount = parseFloat(resource.amount.value);
      const isPartialRefund = refundAmount < payment.amount;

      // Transaction atomique
      const [refund, updatedPayment] = await this.prisma.$transaction([
        this.prisma.refund.create({
          data: {
            paymentId: payment.id,
            amount: refundAmount,
            refundId: resource.id,
            status: 'COMPLETED',
          },
        }),

        this.prisma.payment.update({
          where: { id: payment.id },
          data: {
            status: isPartialRefund ? 'PARTIALLY_REFUNDED' : 'REFUNDED',
            refundedAmount: {
              increment: refundAmount,
            },
          },
        }),
      ]);

      // Notification
      this.eventEmitter.emit('payment.refunded', {
        orderId: payment.orderId,
        refundId: refund.id,
        amount: refundAmount,
        isPartial: isPartialRefund,
      });

      return { status: 'processed' };

    } catch (error) {
      this.logger.error('Refund webhook error', { error });
      throw error;
    }
  }

  private generateVerificationData(
    webhookId: string,
    transmissionId: string,
    payload: unknown,
  ): string {
    return [
      webhookId,
      transmissionId,
      crypto.createHash('sha256').update(JSON.stringify(payload)).digest('hex'),
    ].join('|');
  }

  private verifySignature(
    data: string,
    signature: string,
    publicKey: string,
  ): boolean {
    const verify = crypto.createVerify('SHA256');
    verify.update(data);
    return verify.verify(publicKey, signature, 'base64');
  }
}
