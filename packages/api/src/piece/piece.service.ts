import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { z } from 'zod';

const searchQuerySchema = z.object({
  query: z.string().min(2),
  filters: z.object({
    brand: z.string().optional(),
    category: z.string().optional(),
    inStock: z.boolean().optional()
  }).optional()
});

const deactivatePieceSchema = z.object({
  pieceId: z.string(),
  reason: z.string(),
  userId: z.string()
});

@Injectable()
export class PieceService {
  constructor(
    private prisma: PrismaService,
    private eventEmitter: EventEmitter2
  ) {}

  async search(data: unknown) {
    const { query, filters } = searchQuerySchema.parse(data);

    const pieces = await this.prisma.piece.findMany({
      where: {
        AND: [
          {
            OR: [
              { reference: { contains: query, mode: 'insensitive' } },
              { name: { contains: query, mode: 'insensitive' } },
              { alias: { contains: query, mode: 'insensitive' } }
            ]
          },
          filters?.brand ? { brandId: filters.brand } : {},
          filters?.category ? { categoryId: filters.category } : {},
          filters?.inStock ? { stock: { gt: 0 } } : {}
        ]
      },
      include: {
        brand: true,
        category: true,
        stockMovements: {
          orderBy: { createdAt: 'desc' },
          take: 1
        }
      }
    });

    this.eventEmitter.emit('piece.searched', {
      query,
      results: pieces.length
    });

    return pieces;
  }

  async getSuggestions(term: string) {
    const suggestions = await this.prisma.piece.findMany({
      where: {
        OR: [
          { reference: { startsWith: term, mode: 'insensitive' } },
          { name: { startsWith: term, mode: 'insensitive' } }
        ]
      },
      select: {
        reference: true,
        name: true
      },
      take: 5,
      orderBy: {
        searchCount: 'desc'
      }
    });

    return suggestions;
  }

  async deactivatePiece(data: unknown) {
    const validated = deactivatePieceSchema.parse(data);

    return this.prisma.$transaction(async (tx) => {
      // Get piece
      const piece = await tx.piece.findUnique({
        where: { id: validated.pieceId },
        include: { brand: true }
      });

      if (!piece) {
        throw new NotFoundException('Pièce non trouvée');
      }

      // Update piece status
      const updated = await tx.piece.update({
        where: { id: validated.pieceId },
        data: {
          isActive: false,
          history: {
            create: {
              action: 'deactivated',
              userId: validated.userId,
              details: {
                reason: validated.reason,
                oldStatus: piece.isActive
              }
            }
          }
        }
      });

      // Create notification
      await tx.notification.create({
        data: {
          type: 'piece_deactivated',
          title: 'Pièce désactivée',
          message: `${piece.reference} - ${piece.name} a été désactivée`,
          userId: validated.userId
        }
      });

      // Emit event
      this.eventEmitter.emit('piece.deactivated', {
        piece: updated,
        reason: validated.reason,
        userId: validated.userId
      });

      return updated;
    });
  }
}
