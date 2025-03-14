import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class GammeService {
  constructor(private prisma: PrismaService) {}

  async getPieceById(pieceId: number) {
    const piece = await this.prisma.piece.findFirst({
      where: {
        PIECE_ID: pieceId,
        PIECE_DISPLAY: true
      },
      include: {
        PIECES_MEDIA_IMG: {
          where: { PMI_DISPLAY: true },
          orderBy: { PMI_SORT: 'asc' }
        },
        PIECES_CRITERIA: {
          include: { CRITERIA: true },
          orderBy: [
            { PCL_LEVEL: 'asc' },
            { PCL_SORT: 'asc' }
          ]
        },
        PIECES_MAKER: true,
        PIECES_PRICE: {
          where: { PRI_DISPO: true }
        }
      }
    });

    if (!piece) {
      throw new NotFoundException('Pièce introuvable');
    }

    return {
      id: piece.PIECE_ID,
      name: piece.PIECE_NAME,
      reference: piece.PIECE_REF,
      maker: piece.PIECES_MAKER.PM_NAME,
      images: piece.PIECES_MEDIA_IMG.map(img => ({
        url: `/products/${img.PMI_FOLDER}/${img.PMI_NAME}`
      })),
      criteria: piece.PIECES_CRITERIA.map(c => ({
        name: c.CRITERIA.CRI_NAME,
        value: c.PCL_VALUE,
        unit: c.CRITERIA.CRI_UNIT
      })),
      price: piece.PIECES_PRICE[0]?.PRI_VENTE_TTC || 0
    };
  }
}
