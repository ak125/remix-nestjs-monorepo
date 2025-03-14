import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';
import { Prisma } from '@prisma/client';

@Injectable()
export class PieceService {
  constructor(private prisma: PrismaService) {}

  async getPieceDetails(pieceId: number) {
    const piece = await this.prisma.piece.findFirst({
      where: {
        PIECE_ID: pieceId,
        PIECE_DISPLAY: true
      },
      include: {
        marque: true,
        specifications: true,
        prices: {
          where: { PRI_DISPO: true }
        },
        images: {
          where: { PMI_DISPLAY: true }
        },
        references: {
          include: {
            brand: true
          }
        }
      }
    });

    if (!piece) {
      throw new NotFoundException(`Pièce #${pieceId} non trouvée`);
    }

    return piece;
  }

  async getPieceCompatibilities(pieceId: number) {
    return this.prisma.auto_TYPE.findMany({
      where: {
        PIECES_RELATION_TYPE: {
          some: {
            RTP_PIECE_ID: pieceId
          }
        },
        TYPE_DISPLAY: true
      },
      include: {
        AUTO_MARQUE: true,
        AUTO_MODELE: true,
        AUTO_TYPE_MOTOR_FUEL: true
      },
      orderBy: [
        { TYPE_MARQUE_ID: 'asc' },
        { TYPE_MODELE_ID: 'asc' },
        { TYPE_SORT: 'asc' }
      ]
    });
  }
}
