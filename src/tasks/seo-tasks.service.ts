import { Injectable, Logger } from '@nestjs/common';
import { Cron, CronExpression } from '@nestjs/schedule';
import { PrismaService } from '../prisma/prisma.service';
import { SearchConsoleService } from '../google/search-console.service';
import { ConfigService } from '@nestjs/config';

@Injectable()
export class SeoTasksService {
  private readonly logger = new Logger(SeoTasksService.name);

  constructor(
    private prisma: PrismaService,
    private searchConsole: SearchConsoleService,
    private config: ConfigService
  ) {}

  @Cron(CronExpression.EVERY_DAY_AT_3AM)
  async updateSeoMetrics() {
    try {
      this.logger.log('🔄 Starting SEO metrics update...');
      
      const siteUrl = this.config.get('DOMAIN')!;
      const [sitemaps, analytics] = await Promise.all([
        this.searchConsole.getSitemaps(siteUrl),
        this.searchConsole.getSearchAnalytics(siteUrl)
      ]);

      await this.saveSeoMetrics(analytics);
      
      this.logger.log(`✅ SEO metrics updated successfully: ${analytics.length} pages`);
    } catch (error) {
      this.logger.error('❌ Error updating SEO metrics:', error);
      throw error;
    }
  }

  private async saveSeoMetrics(metrics: any[]) {
    await this.prisma.$transaction(
      metrics.map(metric => 
        this.prisma.seoMetrics.upsert({
          where: {
            pageUrl_date: {
              pageUrl: metric.keys[0],
              date: new Date()
            }
          },
          update: {
            clicks: metric.clicks,
            impressions: metric.impressions,
            ctr: metric.ctr,
            position: metric.position
          },
          create: {
            pageUrl: metric.keys[0],
            clicks: metric.clicks,
            impressions: metric.impressions,
            ctr: metric.ctr,
            position: metric.position,
            date: new Date()
          }
        })
      )
    );
  }
}
