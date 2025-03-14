import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import { Cache } from 'cache-manager';
import { Inject } from '@nestjs/common';

@Injectable()
export class BlogService {
  constructor(
    private prisma: PrismaService,
    @Inject(CACHE_MANAGER) private cacheManager: Cache
  ) {}

  async getModelInfo(params: { marqueAlias: string; mdgAlias: string }) {
    // Get model and brand info
    const model = await this.prisma.autoModele.findFirst({
      where: {
        MODELE_ALIAS: params.mdgAlias,
        MODELE_DISPLAY: true,
        AUTO_MARQUE: {
          MARQUE_ALIAS: params.marqueAlias,
          MARQUE_DISPLAY: true
        }
      },
      include: {
        AUTO_MARQUE: true
      }
    });

    if (!model) {
      throw new NotFoundException('Modèle non trouvé');
    }

    // Get types for model
    const types = await this.prisma.autoType.findMany({
      where: {
        TYPE_MODELE_ID: model.MODELE_ID,
        TYPE_DISPLAY: true
      },
      orderBy: [
        { TYPE_FUEL: 'asc' },
        { TYPE_NAME: 'asc' }
      ]
    });

    // Get popular parts
    const popularParts = await this.prisma.crossGammeCar.findMany({
      where: {
        CGC_MARQUE_ID: model.AUTO_MARQUE.MARQUE_ID,
        CGC_MODELE_ID: model.MODELE_ID, 
        CGC_LEVEL: 2
      },
      include: {
        PIECES_GAMME: true,
        AUTO_TYPE: {
          include: {
            AUTO_MODELE: {
              include: {
                AUTO_MARQUE: true
              }
            }
          }
        }
      }
    });

    return {
      model,
      types,
      popularParts
    };
  }

  async getRecentArticles() {
    const cacheKey = 'recent_articles';
    const cached = await this.cacheManager.get(cacheKey);
    
    if (cached) {
      return cached;
    }

    const articles = await this.prisma.blogAdvice.findMany({
      take: 12,
      orderBy: { BA_UPDATE: 'desc' },
      include: {
        PIECES_GAMME: true,
        CATALOG_GAMME: {
          include: {
            CATALOG_FAMILY: true
          }
        }
      }
    });

    const formatted = articles.map(article => ({
      id: article.BA_ID,
      title: article.BA_H1,
      alias: article.BA_ALIAS,
      preview: article.BA_PREVIEW,
      image: article.BA_WALL,
      updatedAt: article.BA_UPDATE,
      gamme: {
        name: article.PIECES_GAMME.PG_NAME,
        alias: article.PIECES_GAMME.PG_ALIAS,
        image: article.PIECES_GAMME.PG_IMG
      }
    }));

    await this.cacheManager.set(cacheKey, formatted, 3600);
    return formatted;
  }

  async getGuides() {
    const cacheKey = 'blog_guides';
    const cached = await this.cacheManager.get(cacheKey);

    if (cached) {
      return cached;
    }

    const guides = await this.prisma.blogGuide.findMany({
      orderBy: { BG_UPDATE: 'desc' },
      select: {
        BG_ID: true,
        BG_H1: true,
        BG_ALIAS: true,
        BG_PREVIEW: true,
        BG_WALL: true,
        BG_UPDATE: true
      }
    });

    await this.cacheManager.set(cacheKey, guides, 3600);
    return guides;
  }
}
