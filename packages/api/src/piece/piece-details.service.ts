import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { z } from 'zod';

const pieceIdSchema = z.object({
  pieceId: z.string(),
  includeHistory: z.boolean().default(false)
});

@Injectable()
export class PieceDetailsService {
  constructor(private prisma: PrismaService) {}

  async getPieceDetails(data: unknown) {
    const { pieceId, includeHistory } = pieceIdSchema.parse(data);

    const piece = await this.prisma.piece.findFirst({
      where: { id: pieceId },
      include: {
        brand: true,
        category: true,
        specifications: true,
        price: {
          orderBy: { updatedAt: 'desc' },
          take: 1
        },
        ...(includeHistory && {
          history: {
            orderBy: { createdAt: 'desc' },
            take: 10,
            include: {
              user: {
                select: {
                  name: true,
                  role: true
                }
              }
            }
          }
        })
      }
    });

    if (!piece) {
      throw new NotFoundException('Pièce non trouvée');
    }

    // Increment view count
    await this.prisma.piece.update({
      where: { id: pieceId },
      data: { viewCount: { increment: 1 } }
    });

    return piece;
  }
}
