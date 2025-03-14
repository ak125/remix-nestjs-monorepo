import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { ConfigService } from '@nestjs/config';

@Injectable()
export class SitemapService {
  private readonly domain: string;

  constructor(
    private prisma: PrismaService,
    config: ConfigService
  ) {
    this.domain = config.get('DOMAIN') || 'https://www.automecanik.com';
  }

  async generateSitemap() {
    const marques = await this.prisma.autoMarque.findMany({
      where: {
        MARQUE_DISPLAY: true
      },
      include: {
        models: {
          where: {
            MODELE_DISPLAY: true
          },
          include: {
            types: {
              where: {
                TYPE_DISPLAY: true,
                TYPE_RELFOLLOW: true
              }
            }
          }
        }
      }
    });

    return this.buildSitemapXml(marques);
  }

  private buildSitemapXml(marques: any[]) {
    let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
    xml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';

    // URLs des marques
    marques.forEach(marque => {
      xml += this.getMarqueUrl(marque);
      
      // URLs des modèles et types
      marque.models.forEach(model => {
        model.types.forEach(type => {
          xml += this.getTypeUrl(marque, model, type);
        });
      });
    });

    xml += '</urlset>';
    return xml;
  }

  private getMarqueUrl(marque: any) {
    return `  <url>
    <loc>${this.domain}/constructeurs/${marque.MARQUE_ALIAS}-${marque.MARQUE_ID}.html</loc>
    <lastmod>${new Date().toISOString().split('T')[0]}</lastmod>
    <priority>1.0</priority>
  </url>\n`;
  }

  private getTypeUrl(marque: any, model: any, type: any) {
    return `  <url>
    <loc>${this.domain}/constructeurs/${marque.MARQUE_ALIAS}-${marque.MARQUE_ID}/${model.MODELE_ALIAS}-${model.MODELE_ID}/${type.TYPE_ALIAS}-${type.TYPE_ID}.html</loc>
    <lastmod>${new Date().toISOString().split('T')[0]}</lastmod>
    <priority>0.8</priority>
  </url>\n`;
  }
}
