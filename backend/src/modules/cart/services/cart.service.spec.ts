import { Test, TestingModule } from '@nestjs/testing';
import { CartService } from './cart.service';
import { PrismaService } from '../../../prisma/prisma.service';
import { ConfigService } from '@nestjs/config';
import { LoggerService } from '../../../common/services/logger.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { Redis } from 'ioredis';
import { NotFoundException } from '@nestjs/common';

describe('CartService', () => {
  let service: CartService;
  let prisma: PrismaService;
  let redis: Redis;
  let logger: LoggerService;
  let events: EventEmitter2;

  const mockPiece = {
    PIECE_ID: 1,
    PIECES_PRICE: [{
      PRI_VENTE_HT: 10,
      PRI_VENTE_TTC: 12,
      PRI_CONSIGNE_HT: 2,
      PRI_CONSIGNE_TTC: 2.4,
    }],
  };

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        CartService,
        {
          provide: PrismaService,
          useValue: {
            xTR_PIECE: {
              findUnique: jest.fn().mockResolvedValue(mockPiece),
            },
          },
        },
        {
          provide: ConfigService,
          useValue: {
            get: jest.fn().mockReturnValue('test'),
          },
        },
        {
          provide: LoggerService,
          useValue: {
            debug: jest.fn(),
            error: jest.fn(),
          },
        },
        {
          provide: EventEmitter2,
          useValue: {
            emit: jest.fn(),
          },
        },
        {
          provide: Redis,
          useValue: {
            get: jest.fn().mockResolvedValue(null),
            set: jest.fn().mockResolvedValue('OK'),
            del: jest.fn().mockResolvedValue(1),
          },
        },
      ],
    }).compile();

    service = module.get<CartService>(CartService);
    prisma = module.get<PrismaService>(PrismaService);
    redis = module.get<Redis>(Redis);
    logger = module.get<LoggerService>(LoggerService);
    events = module.get<EventEmitter2>(EventEmitter2);
  });

  describe('Standard Cart Operations', () => {
    it('should add an item to cart', async () => {
      await service.updateCart('test-session', 1, 'plus');
      
      expect(redis.set).toHaveBeenCalled();
      expect(events.emit).toHaveBeenCalledWith(
        'cart.updated',
        expect.any(Object)
      );
    });

    it('should remove an item from cart', async () => {
      await service.updateCart('test-session', 1, 'drop');
      
      expect(redis.set).toHaveBeenCalled();
      expect(events.emit).toHaveBeenCalledWith(
        'cart.updated',
        expect.any(Object)
      );
    });

    it('should throw when piece not found', async () => {
      jest.spyOn(prisma.xTR_PIECE, 'findUnique').mockResolvedValueOnce(null);
      
      await expect(
        service.updateCart('test-session', 999, 'plus')
      ).rejects.toThrow(NotFoundException);
    });
  });

  describe('Manuscript Operations', () => {
    it('should add manuscript with options', async () => {
      const manuscriptData = {
        pieceId: 1,
        format: 'A4',
        recto_verso: true,
        quantity: 2,
      };

      await service.addManuscript('test-session', manuscriptData);

      expect(redis.set).toHaveBeenCalled();
      expect(events.emit).toHaveBeenCalledWith(
        'cart.updated',
        expect.objectContaining({
          cart: expect.objectContaining({
            items: expect.objectContaining({
              [manuscriptData.pieceId]: expect.objectContaining({
                type: 'manuscript',
                format: 'A4',
                recto_verso: true,
              }),
            }),
          }),
        }),
      );
    });

    it('should calculate manuscript price correctly', async () => {
      const manuscriptData = {
        pieceId: 1,
        format: 'A4',
        recto_verso: true,
        quantity: 2,
      };

      const cart = await service.addManuscript('test-session', manuscriptData);
      
      // Prix de base * quantité * coefficient format * coefficient recto-verso
      const expectedPrice = 12 * 2 * (manuscriptData.format === 'A3' ? 2 : 1) * (manuscriptData.recto_verso ? 1.8 : 1);
      
      expect(cart.totalAmount).toBe(expectedPrice);
    });
  });

  describe('Copy Operations', () => {
    it('should add copies with options', async () => {
      const copyData = {
        pieceId: 1,
        format: 'A3',
        recto_verso: true,
        copies: 3,
      };

      await service.addCopy('test-session', copyData);

      expect(redis.set).toHaveBeenCalled();
      expect(events.emit).toHaveBeenCalledWith(
        'cart.updated',
        expect.objectContaining({
          cart: expect.objectContaining({
            items: expect.objectContaining({
              [copyData.pieceId]: expect.objectContaining({
                type: 'copy',
                format: 'A3',
                recto_verso: true,
                copies: 3,
              }),
            }),
          }),
        }),
      );
    });

    it('should calculate copies price correctly', async () => {
      const copyData = {
        pieceId: 1,
        format: 'A3',
        recto_verso: true,
        copies: 3,
      };

      const cart = await service.addCopy('test-session', copyData);
      
      // Prix de base * nombre copies * coefficient format * coefficient recto-verso
      const expectedPrice = 12 * 3 * (copyData.format === 'A3' ? 2 : 1) * (copyData.recto_verso ? 1.8 : 1);
      
      expect(cart.totalAmount).toBe(expectedPrice);
    });
  });

  describe('Cart Calculations', () => {
    it('should calculate totals correctly with mixed items', async () => {
      // Ajout pièce standard
      await service.updateCart('test-session', 1, 'plus');
      
      // Ajout manuscrit
      await service.addManuscript('test-session', {
        pieceId: 2,
        format: 'A4',
        recto_verso: true,
        quantity: 2,
      });

      // Ajout photocopies
      await service.addCopy('test-session', {
        pieceId: 3,
        format: 'A3',
        recto_verso: true,
        copies: 3,
      });

      const cart = await service.getCart('test-session');
      
      expect(cart.totalAmount).toBeGreaterThan(0);
      expect(cart.totalConsigne).toBeGreaterThan(0);
    });
  });
});
