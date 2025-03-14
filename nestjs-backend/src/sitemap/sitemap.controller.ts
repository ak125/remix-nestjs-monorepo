import { Controller, Get, Header, Logger, Query, Res } from '@nestjs/common';
import { Response } from 'express';
import { SitemapService } from './sitemap.service';
import { ConfigService } from '@nestjs/config';

@Controller()
export class SitemapController {
  private readonly logger = new Logger(SitemapController.name);
  private readonly baseUrl: string;

  constructor(
    private readonly sitemapService: SitemapService,
    private readonly configService: ConfigService
  ) {
    this.baseUrl = this.configService.get<string>('BASE_URL') || 'https://example.com';
  }

  @Get('sitemap.xml')
  @Header('Content-Type', 'application/xml')
  async generateSitemap(@Res() res: Response, @Query('type') type?: string) {
    try {
      this.logger.log(`Génération du sitemap.xml${type ? ` de type ${type}` : ''}`);
      
      const xml = await this.sitemapService.generateSitemap(type);
      return res.send(xml);
    } catch (error) {
      this.logger.error(`Erreur lors de la génération du sitemap: ${error.message}`, error.stack);
      
      // En cas d'erreur, générer un sitemap minimal
      const fallbackXml = this.sitemapService.generateFallbackSitemap(this.baseUrl);
      return res.send(fallbackXml);
    }
  }

  @Get('sitemap-index.xml')
  @Header('Content-Type', 'application/xml')
  async generateSitemapIndex(@Res() res: Response) {
    try {
      this.logger.log('Génération de l\'index des sitemaps');
      
      const xml = await this.sitemapService.generateSitemapIndex();
      return res.send(xml);
    } catch (error) {
      this.logger.error(`Erreur lors de la génération de l'index des sitemaps: ${error.message}`, error.stack);
      
      // En cas d'erreur, générer un index minimal
      const fallbackXml = this.sitemapService.generateFallbackSitemapIndex(this.baseUrl);
      return res.send(fallbackXml);
    }
  }
}
