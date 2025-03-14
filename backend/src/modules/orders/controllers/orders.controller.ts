import {
  Controller,
  Get,
  Post,
  Patch,
  Param,
  Body,
  UseGuards,
  Req,
  UnauthorizedException,
  BadRequestException,
  Session,
} from '@nestjs/common';
import { Request } from 'express';
import { OrdersService } from '../services/orders.service';
import { LoggerService } from '../../../common/services/logger.service';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { RequireAuth } from '../../../common/decorators/roles.decorator';
import { Role } from '../../../common/enums/roles.enum';
import { z } from 'zod';
import { AuthGuard } from '../../../common/guards/auth.guard';

// Schémas de validation Zod
const OrderIdSchema = z.string();

const EquivalenceParamsSchema = z.object({
  orderId: z.string(),
  orderLineId: z.string().transform(val => parseInt(val, 10)),
});

const ProposeEquivalenceSchema = z.object({
  pieceId: z.string(),
  quantity: z.number().int().positive(),
  priceHT: z.number().positive(),
});

const UpdateOrderLineSchema = z.object({
  supplierId: z.number().optional(),
  priceHT: z.number().positive().optional(),
  quantity: z.number().int().positive().optional(),
});

@Controller('orders')
@UseGuards(AuthGuard, RolesGuard)
export class OrdersController {
  constructor(
    private readonly ordersService: OrdersService,
    private readonly logger: LoggerService,
  ) {}

  // ÉQUIVALENCES

  @Post(':orderId/lines/:orderLineId/equivalence/propose')
  @RequireAuth({
    roles: [Role.Support, Role.Manager],
    permissions: ['orders.equivalence.propose'],
  })
  async proposeEquivalence(
    @Param() params: Record<string, string>,
    @Body() body: unknown,
    @Req() req: Request,
  ) {
    const sessionId = req.cookies?.sessionId;
    if (!sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    // Validation des paramètres
    const paramsResult = EquivalenceParamsSchema.safeParse(params);
    if (!paramsResult.success) {
      throw new BadRequestException(paramsResult.error.format());
    }

    // Validation du body
    const bodyResult = ProposeEquivalenceSchema.safeParse(body);
    if (!bodyResult.success) {
      throw new BadRequestException(bodyResult.error.format());
    }

    const { orderId, orderLineId } = paramsResult.data;

    return this.ordersService.proposeEquivalence(
      orderId,
      orderLineId,
      bodyResult.data,
      sessionId,
    );
  }

  @Patch(':orderId/lines/:orderLineId/equivalence/validate')
  @RequireAuth({
    roles: [Role.Admin, Role.Support],
    permissions: ['orders.equivalence.validate'],
  })
  async validateEquivalence(
    @Param() params: Record<string, string>,
    @Req() req: Request,
  ) {
    const sessionId = req.cookies?.sessionId;
    if (!sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    const paramsResult = EquivalenceParamsSchema.safeParse(params);
    if (!paramsResult.success) {
      throw new BadRequestException(paramsResult.error.format());
    }

    const { orderId, orderLineId } = paramsResult.data;

    return this.ordersService.validateEquivalence(
      orderId,
      orderLineId,
      sessionId,
    );
  }

  // MISE À JOUR DES LIGNES

  @Patch(':orderId/lines/:orderLineId')
  @RequireAuth({
    roles: [Role.Admin, Role.Support],
    permissions: ['orders.update'],
  })
  async updateOrderLine(
    @Param() params: Record<string, string>,
    @Body() body: unknown,
    @Req() req: Request,
  ) {
    const sessionId = req.cookies?.sessionId;
    if (!sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    const paramsResult = EquivalenceParamsSchema.safeParse(params);
    if (!paramsResult.success) {
      throw new BadRequestException(paramsResult.error.format());
    }

    const bodyResult = UpdateOrderLineSchema.safeParse(body);
    if (!bodyResult.success) {
      throw new BadRequestException(bodyResult.error.format());
    }

    const { orderId, orderLineId } = paramsResult.data;

    return this.ordersService.updateOrderLine(
      orderId,
      orderLineId,
      bodyResult.data,
      sessionId,
    );
  }

  // HISTORIQUE

  @Get(':orderId/history')
  @RequireAuth({
    roles: [Role.Admin, Role.Support],
    permissions: ['orders.history'],
  })
  async getOrderHistory(
    @Param('orderId') orderId: string,
    @Req() req: Request,
  ) {
    const sessionId = req.cookies?.sessionId;
    if (!sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    const result = OrderIdSchema.safeParse(orderId);
    if (!result.success) {
      throw new BadRequestException(result.error.format());
    }

    return this.ordersService.getOrderHistory(orderId);
  }

  @Patch(':orderId/cancel')
  async cancelOrder(
    @Param('orderId') orderId: string,
    @Session() session: Record<string, any>,
  ) {
    if (!session.userId) {
      throw new UnauthorizedException('Session requise');
    }

    try {
      return await this.ordersService.cancelOrder(
        parseInt(orderId),
        session.userId,
      );
    } catch (error) {
      this.logger.error('Erreur annulation commande', 'Orders', {
        error,
        orderId,
        userId: session.userId,
      });
      throw error;
    }
  }

  @Get()
  async getCustomerOrders(@Session() session: Record<string, any>) {
    if (!session.userId) {
      throw new UnauthorizedException('Session requise');
    }

    try {
      return await this.ordersService.getOrdersByCustomer(session.userId);
    } catch (error) {
      this.logger.error('Erreur récupération commandes', 'Orders', {
        error,
        userId: session.userId,
      });
      throw error;
    }
  }
}
