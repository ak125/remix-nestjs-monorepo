import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class PiecesService {
  constructor(private prisma: PrismaService) {}

  async getPieceDetails(params: {
    pieceId: number;
    typeId?: number;
  }) {
    const { pieceId, typeId } = params;

    const piece = await this.prisma.piece.findFirst({
      where: {
        PIECE_ID: pieceId,
        PIECE_DISPLAY: true,
        ...(typeId && {
          PIECES_RELATION_TYPE: {
            some: {
              RTP_TYPE_ID: typeId
            }
          }
        })
      },
      include: {
        marque: true,
        prices: {
          where: { PRI_DISPO: true },
          orderBy: { PRI_VENTE_TTC: 'asc' }
        },
        specifications: {
          include: {
            critere: true
          }
        },
        images: {
          where: { PMI_DISPLAY: true },
          take: 1
        }
      }
    });

    if (!piece) {
      throw new NotFoundException(`Piece #${pieceId} not found`);
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
        AUTO_MODELE: true
      },
      orderBy: [
        { TYPE_MARQUE_ID: 'asc' },
        { TYPE_MODELE_ID: 'asc' }
      ]
    });
  }
}
