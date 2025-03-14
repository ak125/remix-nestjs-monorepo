import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class ArticlesService {
  constructor(private prisma: PrismaService) {}

  async findOne(params: {
    pieceId: number;
    typeId: number;
    pgId: number;
    pmId: number;
  }) {
    const article = await this.prisma.piece.findFirst({
      where: {
        PIECE_ID: params.pieceId,
        PIECE_DISPLAY: true,
        PM_ID: params.pmId,
        PG_ID: params.pgId
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
        PIECES_REFERENCE: {
          include: { MAKER: true },
          orderBy: { MAKER_NAME: 'asc' }  
        },
        PIECES_PRICE: {
          where: { PRI_DISPO: true }
        },
        PIECES_STOCK: true,
        PIECES_MAKER: true
      }
    });

    if (!article) return null;

    return {
      article: {
        id: article.PIECE_ID,
        name: article.PIECE_NAME,
        ref: article.PIECE_REF,
        maker: article.PIECES_MAKER.PM_NAME,
        description: article.PIECE_DES
      },
      images: article.PIECES_MEDIA_IMG.map(img => ({
        url: `/products/${img.PMI_FOLDER}/${img.PMI_NAME}`
      })),
      criteria: article.PIECES_CRITERIA.map(c => ({
        name: c.CRITERIA.CRI_NAME,
        value: c.PCL_VALUE,
        unit: c.CRITERIA.CRI_UNIT
      })),
      references: {
        oem: article.PIECES_REFERENCE
          .filter(r => r.REF_TYPE === 'OEM')
          .map(r => ({
            maker: r.MAKER.MAKER_NAME,
            ref: r.REF_NUMBER
          })),
        equip: article.PIECES_REFERENCE
          .filter(r => r.REF_TYPE === 'EQUIP')
          .map(r => ({
            maker: r.MAKER.MAKER_NAME,
            ref: r.REF_NUMBER
          }))
      },
      stock: article.PIECES_STOCK?.PST_QTE || 0,
      price: {
        net: article.PIECES_PRICE[0]?.PRI_VENTE_TTC || 0,
        public: article.PIECES_PRICE[0]?.PRI_PUBLIC_TTC || 0,
        discount: article.PIECES_PRICE[0]?.PRI_REMISE || 0
      }
    };
  }
}
