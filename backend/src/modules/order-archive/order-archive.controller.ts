import { 
  Controller, 
  Get, 
  Param, 
  Query, 
  Req,
  UnauthorizedException,
  BadRequestException,
} from '@nestjs/common';
import { OrderArchiveService } from './order-archive.service';
import { Request } from 'express';
import { z } from 'zod';

// Validation des paramètres de pagination
const PaginationSchema = z.object({
  page: z.coerce.number().min(1).default(1),
  limit: z.coerce.number().min(1).max(100).default(10),
});

@Controller('order-archive')
export class OrderArchiveController {
  constructor(private readonly orderArchiveService: OrderArchiveService) {}

  @Get()
  async getArchivedOrders(
    @Query() query: Record<string, string>,
    @Req() req: Request,
  ) {
    const sessionId = req.cookies.sessionId;
    if (!sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    const result = PaginationSchema.safeParse(query);
    if (!result.success) {
      throw new BadRequestException(result.error.format());
    }

    return this.orderArchiveService.getArchivedOrders(
      result.data.page,
      result.data.limit
    );
  }

  @Get(':orderId')
  async getArchivedOrderById(
    @Param('orderId') orderId: string,
    @Req() req: Request,
  ) {
    const sessionId = req.cookies.sessionId;
    if (!sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    return this.orderArchiveService.getArchivedOrderById(orderId);
  }
}
