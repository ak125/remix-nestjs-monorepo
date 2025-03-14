import { Injectable, Logger, BadRequestException } from '@nestjs/common';
import { PayPalConfigService } from '../config/paypal.config';
import { PrismaService } from '../../../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import * as paypal from '@paypal/checkout-server-sdk';

@Injectable()
export class PayPalService {
  private readonly logger = new Logger(PayPalService.name);

  constructor(
    private readonly paypalConfig: PayPalConfigService,
    private readonly prisma: PrismaService,
    private readonly eventEmitter: EventEmitter2,
  ) {}

  async createOrder(amount: number, orderId: string, paymentMethod?: 'IMMEDIATE' | 'PAY_LATER') {
    try {
      const request = new paypal.orders.OrdersCreateRequest();
      request.requestBody({
        intent: 'CAPTURE',
        purchase_units: [{
          amount: {
            currency_code: 'EUR',
            value: amount.toFixed(2),
          },
          reference_id: orderId,
        }],
        payment_source: {
          paypal: {
            experience_context: {
              payment_method_preference: paymentMethod === 'PAY_LATER' 
                ? 'IMMEDIATE_PAYMENT_REQUIRED'
                : undefined,
              payment_method_selected: paymentMethod === 'PAY_LATER' 
                ? 'PAY_LATER'
                : undefined,
            },
          },
        },
      });

      const client = this.paypalConfig.getClient();
      const response = await client.execute(request);

      await this.prisma.payment.create({
        data: {
          orderId,
          provider: 'PAYPAL',
          providerOrderId: response.result.id,
          amount,
          status: 'PENDING',
        },
      });

      return response.result;

    } catch (error) {
      this.logger.error('Erreur création ordre PayPal', { error, orderId });
      throw new BadRequestException('Erreur création paiement');
    }
  }

  async capturePayment(paypalOrderId: string) {
    try {
      const request = new paypal.orders.OrdersCaptureRequest(paypalOrderId);
      const client = this.paypalConfig.getClient();
      const response = await client.execute(request);

      const payment = await this.prisma.payment.findFirst({
        where: { providerOrderId: paypalOrderId },
      });

      if (!payment) {
        throw new BadRequestException('Paiement non trouvé');
      }

      // Transaction mise à jour
      const [updatedPayment, updatedOrder] = await this.prisma.$transaction([
        this.prisma.payment.update({
          where: { id: payment.id },
          data: {
            status: 'COMPLETED',
            providerTransactionId: response.result.purchase_units[0].payments.captures[0].id,
            isPayLater: response.result.payment_source?.paypal?.payment_method_selected === 'PAY_LATER',
            paymentDetails: response.result,
          },
        }),
        this.prisma.order.update({
          where: { id: payment.orderId },
          data: {
            status: 'PAID',
            isPaid: true,
            paymentDate: new Date(),
          },
        }),
      ]);

      this.eventEmitter.emit('payment.completed', {
        orderId: payment.orderId,
        paymentId: payment.id,
      });

      return response.result;

    } catch (error) {
      this.logger.error('Erreur capture PayPal', { error, paypalOrderId });
      throw new BadRequestException('Erreur capture paiement');
    }
  }
}
