import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { ContentService } from '../../../common/services/content.service';

@Injectable()
export class GammeService {
  private readonly logger = new Logger(GammeService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly contentService: ContentService
  ) {}

  async validateGammeAccess(pgId: string) {
    return await this.prisma.piecesGamme.findFirst({
      where: {
        id: Number(pgId),
        level: { in: [1, 2] }
      },
      select: {
        display: true
      }
    });
  }

  async getGammeWithRelations(pgId: string) {
    try {
      const [details, seo, blog, stats] = await Promise.all([
        this.getGammeDetails(pgId),
        this.getGammeSeo(pgId),
        this.getGammeBlog(pgId),
        this.getGammeStats(pgId)
      ]);

      return {
        details, 
        seo,
        blog,
        stats
      };

    } catch (error) {
      this.logger.error('Error fetching gamme data', error);
      throw error;
    }
  }

  private async getGammeDetails(pgId: string) {
    return this.prisma.piecesGamme.findUnique({
      where: { id: Number(pgId) },
      include: {
        manufacturer: true,
        categories: true
      }
    });
  }

  private async getGammeSeo(pgId: string) {
    const seoData = await this.prisma.seoGamme.findFirst({
      where: { pgId: Number(pgId) }
    });

    if (!seoData) {
      return this.generateDefaultSeo(pgId);
    }

    return this.contentService.cleanSeoContent(seoData);
  }

  // ... autres méthodes privées
}
