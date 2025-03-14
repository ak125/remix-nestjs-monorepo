import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import { Cache } from 'cache-manager';
import { Inject } from '@nestjs/common';

@Injectable()
export class MarqueService {
  constructor(
    private prisma: PrismaService,
    @Inject(CACHE_MANAGER) private cacheManager: Cache
  ) {}

  async getMarqueByAlias(marqueAlias: string) {
    const cacheKey = `marque:${marqueAlias}`;
    const cached = await this.cacheManager.get(cacheKey);
    
    if (cached) return cached;

    const marque = await this.prisma.autoMarque.findFirst({
      where: {
        MARQUE_ALIAS: marqueAlias,
        MARQUE_DISPLAY: true
      },
      select: {
        MARQUE_ID: true,
        MARQUE_NAME: true,
        MARQUE_NAME_META: true,
        MARQUE_ALIAS: true,
        MARQUE_LOGO: true,
        MARQUE_RELFOLLOW: true
      }
    });

    if (!marque) {
      throw new NotFoundException('Marque non trouvée');
    }

    await this.cacheManager.set(cacheKey, marque, 3600);
    return marque;
  }

  async getModelsByMarque(marqueId: number, page = 1, limit = 6) {
    const skip = (page - 1) * limit;

    const [models, total] = await Promise.all([
      this.prisma.autoModele.findMany({
        where: {
          MODELE_MARQUE_ID: marqueId,
          MODELE_DISPLAY: true,
          MODELE_PARENT: 0
        },
        select: {
          MODELE_ID: true,
          MODELE_NAME: true,
          MODELE_ALIAS: true,
          MODELE_PIC: true
        },
        orderBy: { MODELE_NAME: 'asc' },
        skip,
        take: limit
      }),
      this.prisma.autoModele.count({
        where: {
          MODELE_MARQUE_ID: marqueId,
          MODELE_DISPLAY: true,
          MODELE_PARENT: 0
        }
      })
    ]);

    return {
      models,
      pagination: {
        total,
        pages: Math.ceil(total / limit),
        current: page
      }
    };
  }

  async getPopularModels(marqueId: number) {
    return this.prisma.crossGammeCar.findMany({
      where: {
        CGC_MARQUE_ID: marqueId,
        CGC_LEVEL: 1
      },
      select: {
        AUTO_TYPE: {
          select: {
            TYPE_ID: true,
            TYPE_NAME: true,
            TYPE_POWER_PS: true,
            TYPE_YEAR_FROM: true,
            TYPE_YEAR_TO: true,
            AUTO_MODELE: {
              select: {
                MODELE_ID: true,
                MODELE_NAME: true,
                MODELE_ALIAS: true,
                MODELE_PIC: true
              }
            }
          }
        }
      },
      take: 6
    });
  }
}
