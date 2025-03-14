import {
  Controller,
  Get,
  Post,
  Delete,
  Body,
  Param,
  Session,
  BadRequestException,
  UnauthorizedException,
} from '@nestjs/common';
import { CartService } from '../services/cart.service';
import { LoggerService } from '../../../common/services/logger.service';
import { z } from 'zod';

// Schémas de validation
const AddToCartSchema = z.object({
  pieceId: z.number().int().positive('ID article invalide'),
  quantity: z.number().int().min(1, 'Quantité minimale: 1').default(1),
  type: z.enum(['piece', 'manuscript', 'copy']).default('piece'),
  format: z.enum(['A4', 'A3']).optional(),
  recto_verso: z.boolean().optional(),
});

const UpdateCartSchema = z.object({
  pieceId: z.number().int().positive('ID article invalide'),
  action: z.enum(['plus', 'minus', 'drop'], {
    errorMap: () => ({ message: 'Action invalide' }),
  }),
});

@Controller('cart')
export class CartController {
  constructor(
    private readonly cartService: CartService,
    private readonly logger: LoggerService,
  ) {}

  @Get()
  async getCart(@Session() session: Record<string, any>) {
    if (!session.sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    try {
      return await this.cartService.getCart(session.sessionId);
    } catch (error) {
      this.logger.error('Erreur récupération panier', 'Cart', {
        error,
        sessionId: session.sessionId,
      });
      throw error;
    }
  }

  @Post('add')
  async addToCart(
    @Body() body: unknown,
    @Session() session: Record<string, any>,
  ) {
    if (!session.sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    try {
      const result = AddToCartSchema.safeParse(body);
      if (!result.success) {
        throw new BadRequestException(result.error.format());
      }

      const { pieceId, quantity, type, ...options } = result.data;

      switch (type) {
        case 'manuscript':
          return this.cartService.addManuscript(session.sessionId, { pieceId, quantity, ...options });
        case 'copy':
          return this.cartService.addCopy(session.sessionId, { pieceId, quantity, ...options });
        default:
          return this.cartService.addToCart(session.sessionId, pieceId, quantity);
      }
    } catch (error) {
      this.logger.error('Erreur ajout article', 'Cart', {
        error,
        sessionId: session.sessionId,
        body,
      });
      throw error;
    }
  }

  @Post('update')
  async updateCart(
    @Body() body: unknown,
    @Session() session: Record<string, any>,
  ) {
    if (!session.sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    try {
      const result = UpdateCartSchema.safeParse(body);
      if (!result.success) {
        throw new BadRequestException(result.error.format());
      }

      const { pieceId, action } = result.data;

      return await this.cartService.updateCart(
        session.sessionId,
        pieceId,
        action,
      );
      
    } catch (error) {
      this.logger.error('Erreur mise à jour panier', 'Cart', {
        error,
        sessionId: session.sessionId,
        body,
      });
      throw error;
    }
  }

  @Delete(':pieceId')
  async removeFromCart(
    @Param('pieceId') pieceId: string,
    @Session() session: Record<string, any>,
  ) {
    if (!session.sessionId) {
      throw new UnauthorizedException('Session requise');
    }

    try {
      const pieceIdNum = parseInt(pieceId, 10);
      if (isNaN(pieceIdNum)) {
        throw new BadRequestException('ID article invalide');
      }

      return await this.cartService.removeFromCart(session.sessionId, pieceIdNum);
    } catch (error) {
      this.logger.error('Erreur suppression article', 'Cart', {
        error,
        sessionId: session.sessionId,
        pieceId,
      });
      throw error;
    }
  }
}
