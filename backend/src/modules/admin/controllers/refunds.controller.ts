import { 
  Controller, 
  Get, 
  Patch,
  Query, 
  Param,
  Body,
  UseGuards,
  Logger,
} from '@nestjs/common';
import { AdminRefundsService } from '../services/refunds.service';
import { IsAdminGuard } from '../guards/is-admin.guard';
import { z } from 'zod';

const UpdateRefundSchema = z.object({
  status: z.enum(['PENDING', 'COMPLETED', 'REJECTED']),
  reason: z.string().optional(),
});

@Controller('admin/refunds')
@UseGuards(IsAdminGuard)
export class AdminRefundsController {
  private readonly logger = new Logger(AdminRefundsController.name);

  constructor(private readonly refundsService: AdminRefundsService) {}

  @Get()
  async getRefunds(
    @Query('status') status?: string,
    @Query('orderId') orderId?: string,
    @Query('page') page = 1,
    @Query('limit') limit = 20,
  ) {
    return this.refundsService.getRefunds({
      status,
      orderId,
      page: Number(page),
      limit: Number(limit),
    });
  }

  @Patch(':id')
  async updateRefund(
    @Param('id') id: string,
    @Body() body: unknown,
  ) {
    const result = UpdateRefundSchema.safeParse(body);
    if (!result.success) {
      throw new BadRequestException(result.error.format());
    }

    return this.refundsService.updateRefund(id, result.data);
  }
}
