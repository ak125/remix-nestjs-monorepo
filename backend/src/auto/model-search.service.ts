import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class ModelSearchService {
  constructor(private prisma: PrismaService) {}

  async findModels(params: {
    marqueId: number;
    year: number;
    gammeId: number;
  }) {
    const { marqueId, year, gammeId } = params;
    const currentYear = new Date().getFullYear();

    return this.prisma.autoModele.findMany({
      where: {
        MODELE_MARQUE_ID: marqueId,
        MODELE_DISPLAY: true,
        MODELE_YEAR_FROM: { lte: year },
        MODELE_YEAR_TO: {
          gte: year,
          equals: null
        },
        AUTO_TYPE: {
          some: {
            TYPE_DISPLAY: true,
            PIECES_RELATION_TYPE: {
              some: {
                RTP_PG_ID: gammeId,
                PIECES: {
                  PIECE_DISPLAY: true
                }
              }
            }
          }
        }
      },
      select: {
        MODELE_ID: true,
        MODELE_NAME: true,
        MODELE_YEAR_FROM: true,
        MODELE_YEAR_TO: true,
        specifications: true
      },
      orderBy: {
        MODELE_SORT: 'asc'
      }
    });
  }
}
