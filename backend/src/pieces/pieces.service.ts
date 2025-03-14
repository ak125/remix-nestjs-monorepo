import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { Prisma } from '@prisma/client';

@Injectable()
export class PiecesService {
  constructor(private prisma: PrismaService) {}

  async getPieceById(pg_id: number) {
    return this.prisma.piecesGamme.findUnique({
      where: { id: pg_id },
      include: {
        catalogGamme: {
          include: { catalogFamily: true },
        },
        seoData: true,
      },
    });
  }

  async searchPieces(params: {
    query: string;
    gammeId?: number;
    equipementId?: number;
    page?: number;
    limit?: number;
  }) {
    const { query, gammeId, equipementId, page = 1, limit = 20 } = params;
    const offset = (page - 1) * limit;

    const where: Prisma.PieceWhereInput = {
      AND: [
        {
          OR: [
            { PIECE_REF: { contains: query, mode: 'insensitive' } },
            { PIECE_NAME: { contains: query, mode: 'insensitive' } },
            {
              PieceReferences: {
                some: {
                  reference: { contains: query, mode: 'insensitive' }
                }
              }
            }
          ]
        },
        { PIECE_DISPLAY: true },
        ...(gammeId ? [{ PIECE_PG_ID: gammeId }] : []),
        ...(equipementId ? [{ PIECE_PM_ID: equipementId }] : [])
      ]
    };

    const [pieces, total] = await Promise.all([
      this.prisma.piece.findMany({
        where,
        include: {
          gamme: true,
          marque: true,
          prices: {
            where: { PRI_DISPO: true },
            orderBy: { PRI_VENTE_TTC: 'asc' }
          },
          images: {
            where: { PMI_DISPLAY: true },
            take: 1
          },
          specifications: {
            include: {
              critere: true
            },
            orderBy: { PC_SORT: 'asc' }
          }
        },
        orderBy: [
          { PIECE_SORT: 'asc' },
          { prices: { PRI_VENTE_TTC: 'asc' } }
        ],
        skip: offset,
        take: limit
      }),
      this.prisma.piece.count({ where })
    ]);

    return {
      pieces,
      pagination: {
        total,
        pages: Math.ceil(total / limit),
        page,
        limit
      }
    };
  }

  async getSearchFilters(query: string) {
    const [equipements, gammes] = await Promise.all([
      this.prisma.pieceMarque.findMany({
        where: {
          pieces: {
            some: {
              OR: [
                { PIECE_REF: { contains: query } },
                { PIECE_NAME: { contains: query } }
              ]
            }
          },
          PM_DISPLAY: true
        },
        select: {
          PM_ID: true,
          PM_NAME: true,
          PM_LOGO: true,
          _count: {
            select: {
              pieces: true
            }
          }
        },
        orderBy: { PM_SORT: 'asc' }
      }),
      this.prisma.pieceGamme.findMany({
        where: {
          pieces: {
            some: {
              OR: [
                { PIECE_REF: { contains: query } },
                { PIECE_NAME: { contains: query } }
              ]
            }
          },
          PG_DISPLAY: true
        },
        select: {
          PG_ID: true, 
          PG_NAME: true,
          _count: {
            select: {
              pieces: true
            }
          }
        },
        orderBy: { PG_SORT: 'asc' }
      })
    ]);

    return { equipements, gammes };
  }
}
