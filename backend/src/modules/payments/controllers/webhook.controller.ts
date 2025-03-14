import { 
  Controller, 
  Post, 
  Body,
  Headers,
  BadRequestException,
  Logger,
} from '@nestjs/common';
import { WebhookService } from '../services/webhook.service';
import { z } from 'zod';

const WebhookSchema = z.object({
  event_type: z.string(),
  resource: z.object({
    id: z.string(),
    amount: z.object({
      value: z.string(),
      currency_code: z.string(),
    }),
    status: z.string(),
  }),
});

@Controller('webhooks/paypal')
export class PayPalWebhookController {
  private readonly logger = new Logger(PayPalWebhookController.name);

  constructor(private readonly webhookService: WebhookService) {}

  @Post()
  async handleWebhook(
    @Body() payload: unknown,
    @Headers('paypal-transmission-id') transmissionId: string,
    @Headers('paypal-transmission-sig') signature: string,
  ) {
    try {
      // Validation du schema
      const result = WebhookSchema.safeParse(payload);
      if (!result.success) {
        throw new BadRequestException('Invalid webhook payload');
      }

      // Vérification signature
      await this.webhookService.verifyWebhook(payload, transmissionId, signature);

      // Traitement selon le type d'événement
      switch (result.data.event_type) {
        case 'PAYMENT.CAPTURE.REFUNDED':
          return this.webhookService.handleRefund(result.data.resource);
        default:
          this.logger.debug(`Ignored webhook event: ${result.data.event_type}`);
          return { status: 'ignored' };
      }

    } catch (error) {
      this.logger.error('Webhook error', { error, payload });
      throw error;
    }
  }
}
