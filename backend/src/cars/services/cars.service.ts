import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class CarsService {
  constructor(private prisma: PrismaService) {}

  async getCarDetails(params: {
    pg_id: number;
    marque_id: number;
    modele_id: number;
    type_id: number;
    filtre_piece_fil_id: number;
    filtre_psf_id: number;
    filtre_pm_id: number;
  }) {
    // Vérifier le véhicule et la gamme
    const [carType, gamme] = await Promise.all([
      // Vérifier le véhicule
      this.prisma.autoType.findFirst({
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
      }),

      // Vérifier la gamme
      this.prisma.piecesGamme.findFirst({
        where: {
          PG_ID: params.pg_id,
          PG_DISPLAY: true,
          PG_LEVEL: {
            in: [1, 2]
          }
        }
      })
    ]);

    if (!carType || !gamme) return null;

    // Construire la requête de base
    const baseWhereClause = {
      RTP_TYPE_ID: params.type_id,
      RTP_PG_ID: params.pg_id,
      PIECE_DISPLAY: true,
      ...(params.filtre_piece_fil_id > 0 && { 
        PIECE_FIL_ID: params.filtre_piece_fil_id 
      }),
      ...(params.filtre_psf_id > 0 && { 
        RTP_PSF_ID: params.filtre_psf_id 
      }),
      ...(params.filtre_pm_id > 0 && { 
        RTP_PM_ID: params.filtre_pm_id 
      })
    };

    // Récupérer les stats
    const [itemCount, minPrice] = await Promise.all([
      // Nombre d'articles
      this.prisma.piecesRelationType.count({
        where: baseWhereClause
      }),

      // Prix minimum
      this.prisma.piecesPrice.aggregate({
        _min: {
          PRI_VENTE_TTC: true
        },
        where: {
          piece: {
            relationType: {
              some: baseWhereClause
            }
          }
        }
      })
    ]);

    return {
      vehicule: {
        marque: carType.AUTO_MODELE.AUTO_MARQUE.MARQUE_NAME,
        modele: carType.AUTO_MODELE.MODELE_NAME,
        type: carType.TYPE_NAME
      },
      gamme: {
        name: gamme.PG_NAME
      },
      stats: {
        itemCount,
        minPrice: minPrice._min?.PRI_VENTE_TTC || 0
      }
    };
  }

  async getYears(params: { marqueId: number }) {
    const { marqueId } = params;

    const types = await this.prisma.autoType.findMany({
      where: {
        TYPE_MARQUE_ID: marqueId,
        TYPE_DISPLAY: true
      },
      select: {
        TYPE_YEAR_FROM: true,
        TYPE_YEAR_TO: true
      },
      distinct: ['TYPE_YEAR_FROM', 'TYPE_YEAR_TO']
    });

    // Get min and max years
    const years = types.reduce((acc, type) => {
      if (type.TYPE_YEAR_FROM) {
        acc.push(type.TYPE_YEAR_FROM);
      }
      if (type.TYPE_YEAR_TO) {
        acc.push(type.TYPE_YEAR_TO);
      }
      return acc;
    }, [] as number[]);

    const min = Math.min(...years);
    const max = Math.max(...years);

    // Generate years array
    return Array.from(
      { length: max - min + 1 }, 
      (_, i) => max - i
    );
  }

  async getMotorisations(params: {
    marqueId: number;
    year: number;
    modeleId: number;
  }) {
    const { marqueId, year, modeleId } = params;

    return this.prisma.autoType.findMany({
      where: {
        TYPE_MODELE_ID: modeleId,
        TYPE_DISPLAY: true,
        AUTO_MODELE: {
          MODELE_DISPLAY: true,
          AUTO_MARQUE: {
            MARQUE_ID: marqueId,
            MARQUE_DISPLAY: true
          }
        },
        AND: [
          { TYPE_YEAR_FROM: { lte: year } },
          { 
            OR: [
              { TYPE_YEAR_TO: { gte: year } },
              { TYPE_YEAR_TO: null }
            ]
          }
        ]
      },
      include: {
        AUTO_MODELE: {
          include: {
            AUTO_MARQUE: true
          }
        }
      },
      orderBy: [
        { TYPE_NAME: 'asc' },
        { TYPE_POWER_PS: 'asc' }
      ]
    });
  }
}
