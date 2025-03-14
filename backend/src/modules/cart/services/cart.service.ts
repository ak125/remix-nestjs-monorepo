import { Injectable, NotFoundException, Inject } from '@nestjs/common';
import { InjectRedis } from '@nestjs/redis';
import { Redis } from 'ioredis';
import { PrismaService } from '../../../prisma/prisma.service';
import { LoggerService } from '../../../common/services/logger.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { z } from 'zod';

// Schémas et types
const CartItemSchema = z.object({
  pieceId: z.number().int().positive(),
  quantity: z.number().int().min(1),
  priceHT: z.number().positive(),
  priceTTC: z.number().positive(),
  consigneHT: z.number().default(0),
  consigneTTC: z.number().default(0),
});

interface CartData {
  items: Record<number, z.infer<typeof CartItemSchema>>;
  totalAmount: number;
  totalConsigne: number;
  updatedAt: Date;
}

@Injectable()
export class CartService {
  private readonly CART_PREFIX = 'cart:';

  constructor(
    private readonly prisma: PrismaService,
    @InjectRedis() private readonly redis: Redis,
    private readonly logger: LoggerService,
    private readonly eventEmitter: EventEmitter2,
    @Inject('CART_TTL') private readonly cartTtl: number,
  ) {}

  async updateCart(
    sessionId: string,
    pieceId: number,
    action: 'plus' | 'minus' | 'drop'
  ): Promise<CartData> {
    try {
      // Vérifier que l'article existe
      const piece = await this.prisma.xTR_PIECE.findUnique({
        where: { PIECE_ID: pieceId },
        include: {
          PIECES_PRICE: true,
        },
      });

      if (!piece) {
        throw new NotFoundException('Article non trouvé');
      }

      // Récupérer le panier actuel
      const cart = await this.getCart(sessionId);
      const currentItem = cart.items[pieceId];
      const price = piece.PIECES_PRICE[0];

      switch (action) {
        case 'plus':
          cart.items[pieceId] = {
            pieceId,
            quantity: (currentItem?.quantity || 0) + 1,
            priceHT: price.PRI_VENTE_HT,
            priceTTC: price.PRI_VENTE_TTC,
            consigneHT: price.PRI_CONSIGNE_HT || 0,
            consigneTTC: price.PRI_CONSIGNE_TTC || 0,
          };
          break;

        case 'minus':
          if (!currentItem) {
            throw new NotFoundException('Article non trouvé dans le panier');
          }
          if (currentItem.quantity > 1) {
            cart.items[pieceId] = {
              ...currentItem,
              quantity: currentItem.quantity - 1,
            };
          } else {
            delete cart.items[pieceId];
          }
          break;

        case 'drop':
          if (!currentItem) {
            throw new NotFoundException('Article non trouvé dans le panier');
          }
          delete cart.items[pieceId];
          break;
      }

      // Sauvegarder et notifier
      await this.saveCart(sessionId, cart);

      return cart;

    } catch (error) {
      this.logger.error('Erreur mise à jour panier', 'Cart', {
        error,
        sessionId,
        pieceId,
        action,
      });
      throw error;
    }
  }

  async getCart(sessionId: string): Promise<CartData> {
    const key = this.getCartKey(sessionId);
    const data = await this.redis.get(key);

    if (!data) {
      return {
        items: {},
        totalAmount: 0,
        totalConsigne: 0,
        updatedAt: new Date(),
      };
    }

    return JSON.parse(data);
  }

  private async saveCart(sessionId: string, cart: CartData): Promise<void> {
    const key = `${this.CART_PREFIX}${sessionId}`;

    // Calculer les totaux
    cart.totalAmount = Object.values(cart.items).reduce(
      (sum, item) => sum + (item.priceTTC * item.quantity),
      0
    );
    cart.totalConsigne = Object.values(cart.items).reduce(
      (sum, item) => sum + (item.consigneTTC * item.quantity),
      0
    );
    cart.updatedAt = new Date();

    // Sauvegarder avec TTL
    await this.redis.set(
      key,
      JSON.stringify(cart),
      'EX',
      this.cartTtl
    );

    // Notifier
    this.eventEmitter.emit('cart.updated', {
      sessionId,
      cart,
    });
  }
}
