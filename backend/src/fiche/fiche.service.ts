import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class FicheService {
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
        images: {
          where: { PMI_DISPLAY: true },
          take: 1
        },
        specifications: true,
        pieces_list: {
          include: {
            component: true
          }
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

    return {
      reference: piece.PIECE_REF,
      name: piece.PIECE_NAME,
      description: piece.PIECE_DES,
      nameSide: piece.PIECE_NAME_SIDE,
      nameComp: piece.PIECE_NAME_COMP,
      brand: piece.marque,
      specifications: piece.specifications,
      image: piece.images[0] ? 
        `rack/${piece.images[0].PMI_FOLDER}/${piece.images[0].PMI_NAME}.webp` : 
        'upload/articles/no.png',
      components: piece.pieces_list,
      references: piece.references
    };
  }
}
