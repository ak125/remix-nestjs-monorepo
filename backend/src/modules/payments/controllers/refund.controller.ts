import { 
  Controller, 
  Post, 
  Body, 
  UseGuards,
  Logger,
} from '@nestjs/common';
import { RefundService } from '../services/refund.service';
import { AuthGuard } from '../../../common/guards/auth.guard';
import { z } from 'zod';

const RefundSchema = z.object({
  orderId: z.string(),
  amount: z.number().positive().optional(),
});

@Controller('payments/refund')
@UseGuards(AuthGuard)
export class RefundController {
  private readonly logger = new Logger(RefundController.name);

  constructor(private readonly refundService: RefundService) {}

  @Post()
  async refundPayment(@Body() body: unknown) {
    const result = RefundSchema.safeParse(body);
    if (!result.success) {
      throw new BadRequestException(result.error.format());
    }

    return this.refundService.refundPayment(
      result.data.orderId,
      result.data.amount,
    );
  }
}
