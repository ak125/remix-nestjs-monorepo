import { 
  Controller, 
  Post, 
  Body, 
  UseGuards,
  Logger,
} from '@nestjs/common';
import { PayPalService } from '../services/paypal.service';
import { AuthGuard } from '../../../common/guards/auth.guard';
import { z } from 'zod';

const CreateOrderSchema = z.object({
  amount: z.number().positive(),
  orderId: z.string(),
});

const CapturePaymentSchema = z.object({
  paypalOrderId: z.string(),
});

@Controller('payments/paypal')
@UseGuards(AuthGuard)
export class PayPalController {
  private readonly logger = new Logger(PayPalController.name);

  constructor(private readonly paypalService: PayPalService) {}

  @Post('create-order')
  async createOrder(@Body() body: unknown) {
    const result = CreateOrderSchema.safeParse(body);
    if (!result.success) {
      throw new BadRequestException(result.error.format());
    }

    return this.paypalService.createOrder(
      result.data.amount,
      result.data.orderId,
    );
  }

  @Post('capture')
  async capturePayment(@Body() body: unknown) {
    const result = CapturePaymentSchema.safeParse(body);
    if (!result.success) {
      throw new BadRequestException(result.error.format());
    }

    return this.paypalService.capturePayment(result.data.paypalOrderId);
  }
}
