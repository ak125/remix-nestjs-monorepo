import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';
import { ConfigService } from '@nestjs/config';

interface SearchResult {
  success: boolean;
  redirectTo: string;
}

@Injectable()
export class CarSearchService {
  private readonly domain: string;

  constructor(
    private prisma: PrismaService,
    config: ConfigService
  ) {
    this.domain = config.get('DOMAIN') || 'https://www.automecanik.com';
  }

  async findCarByMine(refMine: string): Promise<SearchResult> {
    // 1. Recherche du type_id par référence mine
    const mineType = await this.prisma.autoTypeMine.findFirst({
      where: {
        mine_search: refMine
      }
    });

    if (!mineType) {
      return { success: false, redirectTo: '/welcome' };
    }

    // 2. Recherche des détails du type de voiture
    const carType = await this.prisma.autoType.findFirst({
      where: {
        TYPE_ID: mineType.mine_type_id,
        TYPE_DISPLAY: true,
        AUTO_MARQUE: {
          MARQUE_DISPLAY: true
        },
        AUTO_MODELE: {
          MODELE_DISPLAY: true
        }
      },
      include: {
        AUTO_MARQUE: true,
        AUTO_MODELE: true
      },
      orderBy: {
        TYPE_ID: 'asc'
      }
    });

    if (!carType) {
      return { success: false, redirectTo: '/welcome' };
    }

    // 3. Construction de l'URL de redirection
    const redirectTo = `/searchcar/${carType.AUTO_MARQUE.MARQUE_ALIAS}-${carType.AUTO_MARQUE.MARQUE_ID}/${carType.AUTO_MODELE.MODELE_ALIAS}-${carType.AUTO_MODELE.MODELE_ID}/${carType.TYPE_ALIAS}-${carType.TYPE_ID}`;

    return {
      success: true,
      redirectTo: this.domain + redirectTo
    };
  }
}
