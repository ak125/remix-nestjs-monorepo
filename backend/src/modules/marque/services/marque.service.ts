import { Injectable, Logger, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { ContentService } from '../../../common/services/content.service';

@Injectable()
export class MarqueService {
  private readonly logger = new Logger(MarqueService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly contentService: ContentService
  ) {}

  async getMarqueWithDetails(marqueId: number) {
    const [details, seo, models] = await Promise.all([
      this.getMarqueDetails(marqueId),
      this.getMarqueSeo(marqueId), 
      this.getPopularModels(marqueId)
    ]);

    if (!details || !details.display) {
      throw new NotFoundException('Marque non trouvée ou non active');
    }

    return {
      details,
      seo: this.contentService.cleanSeoContent(seo),
      models,
      meta: {
        canonical: `/auto/${details.alias}-${marqueId}.html`,
        robots: this.generateRobots(details.relFollow)
      }
    };
  }

  private async getMarqueDetails(marqueId: number) {
    return this.prisma.marque.findUnique({
      where: { id: marqueId },
      select: {
        id: true,
        name: true,
        alias: true,
        metaTitle: true,
        display: true,
        logo: true,
        relFollow: true
      }
    });
  }

  private async getMarqueSeo(marqueId: number) {
    const seo = await this.prisma.seoMarque.findFirst({
      where: { marqueId }
    });

    if (!seo) {
      return this.generateDefaultSeo(marqueId);
    }

    return seo;
  }

  private async getPopularModels(marqueId: number) {
    return this.prisma.modele.findMany({
      where: {
        marqueId,
        display: true
      },
      take: 12,
      orderBy: {
        popularity: 'desc'
      },
      include: {
        types: {
          where: {
            display: true
          },
          take: 1
        }
      }
    });
  }

  private generateRobots(relFollow: boolean) {
    return relFollow ? 'index, follow' : 'noindex, nofollow';
  }

  private async generateDefaultSeo(marqueId: number) {
    const marque = await this.getMarqueDetails(marqueId);
    
    return {
      title: `Pièces détachées auto ${marque.metaTitle} neuves & d'origine`,
      description: `Achetez pour votre ${marque.name} des pièces détachées & accessoires auto de qualité à un prix pas cher.`,
      keywords: marque.name,
      h1: `Univers ${marque.name}`,
      content: `Automecanik vous propose tous les modèles du constructeur automobile ${marque.name}.`
    };
  }
}
