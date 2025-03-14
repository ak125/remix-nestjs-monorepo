import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { ConfigService } from '@nestjs/config';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import { Inject } from '@nestjs/common';
import { Cache } from 'cache-manager';
import { EventEmitter2, OnEvent } from '@nestjs/event-emitter';
import axios from 'axios';

type ContentType = 'article' | 'page' | 'piece' | 'model' | 'blog';

@Injectable()
export class SitemapService {
  private readonly logger = new Logger(SitemapService.name);
  private readonly baseUrl: string;

  constructor(
    private readonly prisma: PrismaService,
    private readonly configService: ConfigService,
    @Inject(CACHE_MANAGER) private cacheManager: Cache,
    private eventEmitter: EventEmitter2
  ) {
    this.baseUrl = this.configService.get<string>('BASE_URL') || 'https://example.com';
  }

  async generateSitemap(type?: string): Promise<string> {
    // Utiliser le cache si disponible (cache de 6 heures)
    const cacheKey = `sitemap:${type || 'main'}`;
    const cachedSitemap = await this.cacheManager.get<string>(cacheKey);
    
    if (cachedSitemap) {
      this.logger.debug(`Sitemap récupéré du cache pour ${type || 'main'}`);
      return cachedSitemap;
    }
    
    try {
      // Générer le sitemap en fonction du type
      let urls: Array<{
        loc: string;
        priority: number;
        changefreq: string;
        lastmod?: Date;
      }> = [];
      
      switch (type) {
        case 'pieces':
          urls = await this.getPiecesUrls();
          break;
          
        case 'blog':
          urls = await this.getBlogUrls();
          break;
          
        case 'models':
          urls = await this.getModelsUrls();
          break;
        
        default:
          // Sitemap principal - pages statiques
          urls = this.getStaticUrls();
      }
      
      // Générer le XML
      let xml = this.generateXmlHeader();
      
      // Ajouter chaque URL
      urls.forEach(url => {
        xml += '  <url>\n';
        xml += `    <loc>${this.baseUrl}${url.loc}</loc>\n`;
        
        if (url.lastmod) {
          xml += `    <lastmod>${url.lastmod.toISOString().split('T')[0]}</lastmod>\n`;
        }
        
        xml += `    <changefreq>${url.changefreq}</changefreq>\n`;
        xml += `    <priority>${url.priority.toFixed(1)}</priority>\n`;
        xml += '  </url>\n';
      });
      
      xml += '</urlset>';
      
      // Mettre en cache pour 6 heures
      await this.cacheManager.set(cacheKey, xml, 6 * 60 * 60 * 1000);
      
      return xml;
    } catch (error) {
      this.logger.error(`Erreur lors de la génération du sitemap ${type || 'main'}: ${error.message}`, error.stack);
      throw error;
    }
  }

  async generateSitemapIndex(): Promise<string> {
    // Utiliser le cache si disponible (cache de 12 heures)
    const cacheKey = 'sitemap:index';
    const cachedIndex = await this.cacheManager.get<string>(cacheKey);
    
    if (cachedIndex) {
      this.logger.debug('Index des sitemaps récupéré du cache');
      return cachedIndex;
    }
    
    // Définir les types de sitemaps disponibles
    const sitemapTypes = ['main', 'pieces', 'blog', 'models'];
    const now = new Date().toISOString().split('T')[0];
    
    let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
    xml += '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';
    
    // Ajouter chaque sitemap
    sitemapTypes.forEach(type => {
      const url = type === 'main' ? '/sitemap.xml' : `/sitemap.xml?type=${type}`;
      
      xml += '  <sitemap>\n';
      xml += `    <loc>${this.baseUrl}${url}</loc>\n`;
      xml += `    <lastmod>${now}</lastmod>\n`;
      xml += '  </sitemap>\n';
    });
    
    xml += '</sitemapindex>';
    
    // Mettre en cache pour 12 heures
    await this.cacheManager.set(cacheKey, xml, 12 * 60 * 60 * 1000);
    
    return xml;
  }

  generateFallbackSitemap(baseUrl: string): string {
    let xml = this.generateXmlHeader();
    
    // Ajouter seulement les pages principales
    const mainPages = [
      { loc: '/', priority: 1.0, changefreq: 'daily' },
      { loc: '/contact', priority: 0.5, changefreq: 'monthly' }
    ];
    
    mainPages.forEach(page => {
      xml += '  <url>\n';
      xml += `    <loc>${baseUrl}${page.loc}</loc>\n`;
      xml += `    <changefreq>${page.changefreq}</changefreq>\n`;
      xml += `    <priority>${page.priority.toFixed(1)}</priority>\n`;
      xml += '  </url>\n';
    });
    
    xml += '</urlset>';
    return xml;
  }

  generateFallbackSitemapIndex(baseUrl: string): string {
    let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
    xml += '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';
    
    xml += '  <sitemap>\n';
    xml += `    <loc>${baseUrl}/sitemap.xml</loc>\n`;
    xml += `    <lastmod>${new Date().toISOString().split('T')[0]}</lastmod>\n`;
    xml += '  </sitemap>\n';
    
    xml += '</sitemapindex>';
    return xml;
  }

  private generateXmlHeader(): string {
    return '<?xml version="1.0" encoding="UTF-8"?>\n' +
           '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';
  }

  private getStaticUrls(): Array<{ loc: string; priority: number; changefreq: string }> {
    return [
      { loc: '/', priority: 1.0, changefreq: 'daily' },
      { loc: '/blog', priority: 0.8, changefreq: 'daily' },
      { loc: '/models', priority: 0.8, changefreq: 'weekly' },
      { loc: '/contact', priority: 0.5, changefreq: 'monthly' },
      { loc: '/faq', priority: 0.6, changefreq: 'monthly' },
      { loc: '/about', priority: 0.6, changefreq: 'monthly' }
    ];
  }

  private async getPiecesUrls(): Promise<Array<{
    loc: string;
    priority: number;
    changefreq: string;
    lastmod?: Date;
  }>> {
    try {
      // Récupérer les pièces depuis la base de données
      const pieces = await this.prisma.piece.findMany({
        where: { display: true },
        select: {
          id: true,
          name: true,
          updatedAt: true
        },
        orderBy: {
          updatedAt: 'desc'
        },
        take: 10000 // Limite pour éviter une surcharge
      });

      return pieces.map(piece => ({
        loc: `/pieces/${piece.id}`,
        priority: 0.7,
        changefreq: 'weekly',
        lastmod: piece.updatedAt
      }));
    } catch (error) {
      this.logger.error('Erreur lors de la récupération des pièces pour le sitemap:', error);
      return [];
    }
  }

  private async getBlogUrls(): Promise<Array<{
    loc: string;
    priority: number;
    changefreq: string;
    lastmod?: Date;
  }>> {
    try {
      // Récupérer les articles de blog
      const blogArticles = await this.prisma.articleBlog.findMany({
        where: { published: true },
        select: {
          slug: true,
          updatedAt: true
        },
        orderBy: {
          updatedAt: 'desc'
        },
        take: 10000 // Limite pour éviter une surcharge
      });

      return blogArticles.map(article => ({
        loc: `/blog/${article.slug}`,
        priority: 0.6,
        changefreq: 'weekly',
        lastmod: article.updatedAt
      }));
    } catch (error) {
      this.logger.error('Erreur lors de la récupération des articles pour le sitemap:', error);
      return [];
    }
  }

  private async getModelsUrls(): Promise<Array<{
    loc: string;
    priority: number;
    changefreq: string;
    lastmod?: Date;
  }>> {
    try {
      // Récupérer les modèles de voiture
      const models = await this.prisma.autoModele.findMany({
        where: { display: true },
        select: {
          id: true,
          alias: true,
          marque: {
            select: {
              alias: true
            }
          }
        },
        take: 10000 // Limite pour éviter une surcharge
      });

      return models.map(model => ({
        loc: `/constructeurs/${model.marque.alias}/${model.alias}`,
        priority: 0.7,
        changefreq: 'monthly',
        lastmod: new Date() // Date actuelle comme fallback
      }));
    } catch (error) {
      this.logger.error('Erreur lors de la récupération des modèles pour le sitemap:', error);
      return [];
    }
  }

  async invalidateSitemapCache(): Promise<void> {
    try {
      // Supprimer tous les caches de sitemap
      const keys = await this.cacheManager.store.keys('sitemap:*');
      
      if (keys.length > 0) {
        await Promise.all(keys.map(key => this.cacheManager.del(key)));
        this.logger.log(`${keys.length} caches de sitemap invalidés`);
      }
    } catch (error) {
      this.logger.error(`Erreur lors de l'invalidation des caches de sitemap: ${error.message}`, error.stack);
    }
  }

  /**
   * Gestion des événements de modification/suppression de contenu
   */
  @OnEvent('content.deleted')
  async handleContentDeleted(payload: { type: ContentType, slug?: string, id?: string | number }) {
    this.logger.log(`Contenu supprimé détecté: ${payload.type} ${payload.slug || payload.id}`);
    await this.invalidateRelevantSitemaps(payload.type);
    
    // Notifier les moteurs de recherche après la suppression
    await this.notifySearchEngines();
  }
  
  @OnEvent('content.created')
  async handleContentCreated(payload: { type: ContentType, slug?: string, id?: string | number }) {
    this.logger.log(`Nouveau contenu détecté: ${payload.type} ${payload.slug || payload.id}`);
    await this.invalidateRelevantSitemaps(payload.type);
    
    // Notifier les moteurs de recherche après la création
    await this.notifySearchEngines();
  }
  
  @OnEvent('content.updated')
  async handleContentUpdated(payload: { type: ContentType, slug?: string, id?: string | number }) {
    this.logger.log(`Contenu mis à jour détecté: ${payload.type} ${payload.slug || payload.id}`);
    await this.invalidateRelevantSitemaps(payload.type);
    
    // Notifier les moteurs de recherche après la mise à jour
    await this.notifySearchEngines();
  }

  /**
   * Invalide seulement les caches sitemap pertinents selon le type de contenu modifié
   */
  private async invalidateRelevantSitemaps(contentType: ContentType) {
    const keysToDelete: string[] = ['sitemap:index']; // L'index est toujours invalidé
    
    // Ajouter les types de sitemaps spécifiques à invalider
    switch (contentType) {
      case 'article':
      case 'blog':
        keysToDelete.push('sitemap:blog');
        break;
      case 'piece':
        keysToDelete.push('sitemap:pieces');
        break;
      case 'model':
        keysToDelete.push('sitemap:models');
        break;
      case 'page':
        keysToDelete.push('sitemap:main'); // Les pages statiques sont dans le sitemap principal
        break;
    }
    
    // Invalider les caches sélectionnés
    this.logger.log(`Invalidation des caches de sitemap: ${keysToDelete.join(', ')}`);
    await Promise.all(keysToDelete.map(key => this.cacheManager.del(key)));
    
    // Aussi notifier le système de mise à jour des URLs désactivées
    this.eventEmitter.emit('sitemap.updated');
  }
  
  /**
   * Récupère les URLs qui ne doivent plus être indexées
   */
  async getDisallowedUrls(): Promise<string[]> {
    try {
      const disallowedUrls: string[] = [];
      
      // Récupérer les articles désactivés
      const disabledArticles = await this.prisma.articleBlog.findMany({
        where: { published: false },
        select: { slug: true }
      });
      disallowedUrls.push(...disabledArticles.map(article => `/blog/${article.slug}`));
      
      // Récupérer les pages désactivées
      const disabledPages = await this.prisma.page.findMany({
        where: { active: false },
        select: { slug: true }
      });
      disallowedUrls.push(...disabledPages.map(page => `/page/${page.slug}`));
      
      // URLs "gone" (anciennes URLs qui n'existent plus)
      const goneUrls = await this.prisma.goneUrl.findMany({
        select: { path: true }
      });
      disallowedUrls.push(...goneUrls.map(url => url.path));
      
      return disallowedUrls;
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération des URLs désactivées: ${error.message}`, error.stack);
      return [];
    }
  }
  
  /**
   * Ajoute une URL à la liste des URLs désactivées (pour robots.txt)
   */
  async addDisallowedUrl(path: string, reason?: string): Promise<void> {
    try {
      await this.prisma.goneUrl.create({
        data: {
          path,
          reason: reason || 'Content removed'
        }
      });
      
      // Émettre un événement pour mettre à jour robots.txt
      this.eventEmitter.emit('robots.url.added', { path });
    } catch (error) {
      this.logger.error(`Erreur lors de l'ajout de l'URL désactivée: ${error.message}`, error.stack);
    }
  }

  /**
   * Notifie les moteurs de recherche de la mise à jour du sitemap
   */
  async notifySearchEngines(): Promise<void> {
    const sitemapIndexUrl = `${this.baseUrl}/sitemap-index.xml`;
    this.logger.log(`Notification des moteurs de recherche pour la mise à jour du sitemap: ${sitemapIndexUrl}`);
    
    try {
      // Notification à Google
      const googlePingUrl = `https://www.google.com/ping?sitemap=${encodeURIComponent(sitemapIndexUrl)}`;
      const googleResponse = await axios.get(googlePingUrl);
      this.logger.log(`Google notification status: ${googleResponse.status}`);
      
      // Notification à Bing
      const bingPingUrl = `https://www.bing.com/ping?sitemap=${encodeURIComponent(sitemapIndexUrl)}`;
      const bingResponse = await axios.get(bingPingUrl);
      this.logger.log(`Bing notification status: ${bingResponse.status}`);
      
      this.logger.log('Moteurs de recherche notifiés avec succès');
    } catch (error) {
      this.logger.error(`Erreur lors de la notification des moteurs de recherche: ${error.message}`, error.stack);
    }
  }
  
  /**
   * Invalide les caches du sitemap et notifie les moteurs de recherche
   * Méthode publique pouvant être appelée manuellement via l'API
   */
  async refreshSitemapAndNotify(): Promise<boolean> {
    try {
      // Invalider tous les caches
      await this.invalidateSitemapCache();
      
      // Notifier les moteurs de recherche
      await this.notifySearchEngines();
      
      return true;
    } catch (error) {
      this.logger.error(`Erreur lors du rafraîchissement du sitemap: ${error.message}`, error.stack);
      return false;
    }
  }
}
