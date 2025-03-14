import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { ConfigService } from '@nestjs/config';

interface RobotRule {
  userAgent: string;
  disallow: string[];
}

interface SitemapConfig {
  url: string;
  priority?: string;
}

interface RobotsConfig {
  rules: RobotRule[];
  sitemaps: SitemapConfig[];
  crawlDelay?: number;
  host?: string;
}

@Injectable()
export class RobotsService {
  private readonly logger = new Logger(RobotsService.name);
  private defaultRules = [
    {
      userAgent: '*',
      disallow: [
        '/_form.get.car.*',
        '/find/',
        '/searchmine/',
        '/account/',
        '/cart/',
        '/checkout/',
        '/order-history/',
        '/admin/',
        '/login/',
        '/register/'
      ]
    }
  ];

  constructor(
    private readonly prisma: PrismaService,
    private readonly configService: ConfigService
  ) {
    this.initializeDefaultRules();
  }

  private async initializeDefaultRules() {
    try {
      const ruleCount = await this.prisma.robotsRule.count();
      
      // Si aucune règle n'existe, créer les règles par défaut
      if (ruleCount === 0) {
        this.logger.log('Initialisation des règles robots.txt par défaut');
        
        // Créer la règle globale
        const globalRule = await this.prisma.robotsRule.create({
          data: {
            userAgent: '*',
            sortOrder: 1
          }
        });
        
        // Ajouter les chemins à bloquer
        for (const path of this.defaultRules[0].disallow) {
          await this.prisma.robotsDisallow.create({
            data: {
              path,
              ruleId: globalRule.id
            }
          });
        }
        
        // Créer le sitemap par défaut
        const baseUrl = this.configService.get<string>('BASE_URL') || 'https://example.com';
        await this.prisma.robotsSitemap.create({
          data: {
            url: `${baseUrl}/sitemap.xml`,
            priority: '1.0'
          }
        });
        
        // Définir le crawl delay par défaut
        await this.prisma.robotsConfig.create({
          data: {
            key: 'crawl-delay',
            value: '10'
          }
        });
        
        // Définir le host par défaut
        await this.prisma.robotsConfig.create({
          data: {
            key: 'host',
            value: baseUrl
          }
        });
      }
    } catch (error) {
      this.logger.error(`Erreur lors de l'initialisation des règles robots.txt: ${error.message}`, error.stack);
    }
  }

  async getRules(): Promise<RobotRule[]> {
    try {
      const rules = await this.prisma.robotsRule.findMany({
        include: {
          disallowRules: true
        },
        orderBy: {
          sortOrder: 'asc'
        }
      });
      
      return rules.map(rule => ({
        userAgent: rule.userAgent,
        disallow: rule.disallowRules.map(dr => dr.path)
      }));
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération des règles robots.txt: ${error.message}`, error.stack);
      return this.defaultRules;
    }
  }

  async getSitemaps(): Promise<SitemapConfig[]> {
    try {
      const sitemaps = await this.prisma.robotsSitemap.findMany({
        orderBy: {
          priority: 'desc'
        }
      });
      
      return sitemaps.map(sitemap => ({
        url: sitemap.url,
        priority: sitemap.priority
      }));
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération des sitemaps: ${error.message}`, error.stack);
      
      const baseUrl = this.configService.get<string>('BASE_URL') || 'https://example.com';
      return [{ url: `${baseUrl}/sitemap.xml` }];
    }
  }

  async getCrawlDelay(): Promise<number | undefined> {
    try {
      const config = await this.prisma.robotsConfig.findUnique({
        where: { key: 'crawl-delay' }
      });
      
      return config ? parseInt(config.value, 10) : 10;
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération du crawl delay: ${error.message}`, error.stack);
      return 10;
    }
  }

  async getHost(): Promise<string | undefined> {
    try {
      const config = await this.prisma.robotsConfig.findUnique({
        where: { key: 'host' }
      });
      
      return config?.value;
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération du host: ${error.message}`, error.stack);
      return this.configService.get<string>('BASE_URL') || 'https://example.com';
    }
  }

  async updateConfiguration(config: RobotsConfig) {
    try {
      // Commencer une transaction pour mettre à jour toutes les configurations
      await this.prisma.$transaction(async (prisma) => {
        // 1. Mettre à jour les règles
        if (config.rules) {
          // Supprimer toutes les règles existantes
          await prisma.robotsDisallow.deleteMany();
          await prisma.robotsRule.deleteMany();
          
          // Créer les nouvelles règles
          for (const [index, rule] of config.rules.entries()) {
            const newRule = await prisma.robotsRule.create({
              data: {
                userAgent: rule.userAgent,
                sortOrder: index + 1
              }
            });
            
            // Créer les règles Disallow pour cette règle
            for (const path of rule.disallow || []) {
              await prisma.robotsDisallow.create({
                data: {
                  path,
                  ruleId: newRule.id
                }
              });
            }
          }
        }
        
        // 2. Mettre à jour les sitemaps
        if (config.sitemaps) {
          await prisma.robotsSitemap.deleteMany();
          
          for (const [index, sitemap] of config.sitemaps.entries()) {
            await prisma.robotsSitemap.create({
              data: {
                url: sitemap.url,
                priority: sitemap.priority || (1.0 - (index * 0.1)).toString()
              }
            });
          }
        }
        
        // 3. Mettre à jour le crawl delay
        if (config.crawlDelay !== undefined) {
          await prisma.robotsConfig.upsert({
            where: { key: 'crawl-delay' },
            update: { value: config.crawlDelay.toString() },
            create: { key: 'crawl-delay', value: config.crawlDelay.toString() }
          });
        }
        
        // 4. Mettre à jour le host
        if (config.host) {
          await prisma.robotsConfig.upsert({
            where: { key: 'host' },
            update: { value: config.host },
            create: { key: 'host', value: config.host }
          });
        }
      });
      
      this.logger.log('Configuration robots.txt mise à jour avec succès');
      return true;
    } catch (error) {
      this.logger.error(`Erreur lors de la mise à jour de la configuration robots.txt: ${error.message}`, error.stack);
      throw error;
    }
  }
}
