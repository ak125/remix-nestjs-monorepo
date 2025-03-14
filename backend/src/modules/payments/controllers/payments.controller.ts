import { Controller, Post, Body, Logger, BadRequestException, Headers } from '@nestjs/common';
import { PaymentsService } from '../services/payments.service';
import { z } from 'zod';

const PaymentDataSchema = z.object({
  signature: z.string(),
  vads_order_id: z.string(),
  vads_result: z.string(),
  vads_amount: z.string().transform(val => parseInt(val, 10)),
  vads_card_brand: z.string(),
  vads_card_country: z.string(),
  vads_trans_id: z.string(),
});

@Controller('payments')
export class PaymentsController {
  private readonly logger = new Logger(PaymentsController.name);

  constructor(private readonly paymentsService: PaymentsService) {}

  @Post('process')
  async processPayment(@Body() body: unknown) {
    try {
      const result = PaymentDataSchema.safeParse(body);
      if (!result.success) {
        throw new BadRequestException('Données de paiement invalides');
      }

      return await this.paymentsService.processPayment(result.data);

    } catch (error) {
      this.logger.error('Erreur traitement paiement', {
        error,
        body,
      });
      throw error;
    }
  }

  @Post('webhook')
  async handleWebhook(
    @Body() payload: unknown,
    @Headers('x-signature') signature: string,
  ) {
    try {
      const validatedPayload = await this.paymentsService.verifyWebhookSignature(
        payload,
        signature,
      );

      return await this.paymentsService.processWebhook(validatedPayload);

    } catch (error) {
      this.logger.error('Erreur webhook paiement', {
        error,
        payload,
      });
      throw error;
    }
  }
}
