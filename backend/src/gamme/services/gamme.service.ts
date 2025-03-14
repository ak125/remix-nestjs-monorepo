import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class GammeService {
  constructor(private prisma: PrismaService) {}

  async getGammeDetails(params: {
    pg_id: number;
    marque_id: number;
    modele_id: number;
    type_id: number;
  }) {
    // Vérification du véhicule
    const carType = await this.prisma.autoType.findFirst({
      where: {
        TYPE_ID: params.type_id,
        TYPE_DISPLAY: true,
        AUTO_MODELE: {
          MODELE_ID: params.modele_id,
          MODELE_DISPLAY: true,
          AUTO_MARQUE: {
            MARQUE_ID: params.marque_id,
            MARQUE_DISPLAY: true
          }
        }
      },
      include: {
        AUTO_MODELE: {
          include: {
            AUTO_MARQUE: true
          }
        }
      }
    });

    if (!carType) {
      throw new NotFoundException('Véhicule non trouvé');
    }

    // Vérification de la gamme
    const gamme = await this.prisma.piecesGamme.findFirst({
      where: {
        PG_ID: params.pg_id,
        PG_DISPLAY: true,
        PG_LEVEL: { in: [1, 2] }
      },
      include: {
        CATALOG_GAMME: {
          include: {
            CATALOG_FAMILY: true
          }
        }
      }
    });

    if (!gamme) {
      throw new NotFoundException('Gamme non trouvée');
    }

    // Stats
    const [articleCount, minPrice] = await Promise.all([
      // Nombre d'articles
      this.prisma.piecesRelationType.count({
        where: {
          RTP_TYPE_ID: params.type_id,
          RTP_PG_ID: params.pg_id,
          PIECE: { PIECE_DISPLAY: true }
        }
      }),

      // Prix minimum
      this.prisma.piecesPrice.aggregate({
        _min: { PRI_VENTE_TTC: true },
        where: {
          PIECE: {
            PIECES_RELATION_TYPE: {
              some: {
                RTP_TYPE_ID: params.type_id,
                RTP_PG_ID: params.pg_id,
                PIECE_DISPLAY: true
              }
            }
          }
        }
      })
    ]);

    return {
      car: carType,
      gamme: gamme,
      stats: {
        articleCount,
        minPrice: minPrice._min?.PRI_VENTE_TTC || 0
      }
    };
  }
}
