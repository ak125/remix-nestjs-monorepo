import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { Prisma } from '@prisma/client';

@Injectable()
export class SearchService {
  constructor(private prisma: PrismaService) {}

  async searchPieces(params: {
    query: string;
    equipementId?: number;
    gammeId?: number; 
    page?: number;
    limit?: number;
  }) {
    const { query, equipementId, gammeId, page = 1, limit = 20 } = params;
    const offset = (page - 1) * limit;

    const where: Prisma.PieceWhereInput = {
      AND: [
        {
          OR: [
            { PIECE_REF: { contains: query, mode: 'insensitive' } },
            { PIECE_NAME: { contains: query, mode: 'insensitive' } },
          ],
        },
        { PIECE_DISPLAY: true },
        ...(equipementId ? [{ PIECE_PM_ID: equipementId }] : []),
        ...(gammeId ? [{ PIECE_PG_ID: gammeId }] : []),
      ],
    };

    const [pieces, total] = await Promise.all([
      this.prisma.piece.findMany({
        where,
        include: {
          equipement: true,
          specifications: true,
          prices: {
            where: { isActive: true }
          }
        },
        orderBy: { PIECE_SORT: 'asc' },
        skip: offset,
        take: limit,
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

  async getFilters(query: string) {
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
        orderBy: { PG_SORT: 'asc' }
      })
    ]);

    return { equipements, gammes };
  }
}
