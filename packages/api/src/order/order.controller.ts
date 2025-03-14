import { Controller, Get, Post, Body, Query, UseGuards } from '@nestjs/common';
import { OrderService } from './order.service';
import { JwtAuthGuard } from '../auth/jwt-auth.guard';
import { User } from '../decorators/user.decorator';

@Controller('orders')
@UseGuards(JwtAuthGuard)
export class OrderController {
  constructor(private order: OrderService) {}

  @Post()
  async createOrder(
    @Body() data: unknown,
    @User('id') userId: string
  ) {
    return this.order.createOrder({
      ...data,
      clientId: userId
    });
  }

  @Get()
  async getOrders(
    @Query('status') status?: string,
    @Query('from') from?: string,
    @Query('to') to?: string
  ) {
    return this.order.getOrders({ status, from, to });
  }

  // ... other endpoints
}
