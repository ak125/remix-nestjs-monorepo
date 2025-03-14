import { 
  Controller, 
  Post, 
  Body,
  Session,
  UseGuards,
  BadRequestException,
  Logger,
} from '@nestjs/common';
import { PaymentService } from '../services/payment.service';
import { LoggerService } from '../../../common/services/logger.service';
import { AuthGuard } from '../../../common/guards/auth.guard';
import { z } from 'zod';

// Validation schema
const PaymentDataSchema = z.object({
  vads_order_id: z.string(),
  vads_auth_number: z.string(),
  vads_amount: z.number().positive(),
  vads_card_brand: z.string(),
  vads_card_country: z.string(),
  vads_trans_id: z.string(),
  vads_result: z.string(),
  signature: z.string(),
});

const FailedPaymentSchema = z.object({
  commande_id: z.string(),
  error: z.string(),
  errorCode: z.string().optional(),
});

@Controller('payments')
@UseGuards(AuthGuard)
export class PaymentController {
  private readonly logger = new Logger(PaymentController.name);

  constructor(
    private readonly paymentService: PaymentService,
    private readonly loggerService: LoggerService,
  ) {}

  @Post('process')
  async processPayment(
    @Body() body: unknown,
    @Session() session: Record<string, any>,
  ) {
    try {
      const result = PaymentDataSchema.safeParse(body);
      if (!result.success) {
        throw new BadRequestException(result.error.format());
      }

      return await this.paymentService.processPayment(
        result.data,
        session.user,
      );

    } catch (error) {
      this.loggerService.error('Erreur traitement paiement', 'Payments', {
        error,
        userId: session.user?.id,
      });
      throw error;
    }
  }

  @Post('failed')
  async handlePaymentFailure(@Body() body: unknown) {
    try {
      const result = FailedPaymentSchema.safeParse(body);
      if (!result.success) {
        throw new BadRequestException(result.error.format());
      }

      return await this.paymentService.handleFailedPayment(
        result.data.commande_id,
        result.data.error,
        result.data.errorCode
      );

    } catch (error) {
      this.logger.error('Erreur paiement échoué', {
        error,
        orderId: body?.commande_id,
      });
      throw error;
    }
  }
}
