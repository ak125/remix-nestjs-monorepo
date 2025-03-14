import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/prisma/prisma.service';

@Injectable()
export class SearchService {
  constructor(private prisma: PrismaService) {}

  async searchPieces(query: string) {
    const cleanedQuery = query.trim().toLowerCase();

    const results = await this.prisma.piece.findMany({
      where: {
        PIECES_REF_SEARCH: {
          some: {
            PRS_SEARCH: cleanedQuery,
          }
        },
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
        PIECES_PRICE: {
          where: { PRI_DISPO: true }
        },
        PIECES_STOCK: true,
        PIECES_MAKER: true
      }
    });

    // Map results to clean format
    const mappedResults = results.map(piece => ({
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
      stock: piece.PIECES_STOCK?.PST_QTE || 0,
      price: {
        net: piece.PIECES_PRICE[0]?.PRI_VENTE_TTC || 0,
        consigne: piece.PIECES_PRICE[0]?.PRI_CONSIGNE_TTC || 0
      }
    }));

    return {
      query: cleanedQuery,
      count: mappedResults.length,
      results: mappedResults
    };
  }
}
