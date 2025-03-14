import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import { Inject } from '@nestjs/common';
import { Cache } from 'cache-manager';
import { OnEvent } from '@nestjs/event-emitter';

@Injectable()
export class DisabledUrlsService {
  private readonly logger = new Logger(DisabledUrlsService.name);
  private readonly CACHE_KEY = 'disabled-urls';
  private readonly CACHE_TTL = 24 * 60 * 60; // 24 heures en secondes

  constructor(
    private readonly prisma: PrismaService,
    @Inject(CACHE_MANAGER) private cacheManager: Cache
  ) {}

  /**
   * Récupère toutes les URLs désactivées (pour robots.txt)
   */
  async getDisallowedUrls(): Promise<string[]> {
    // Vérifier si les données sont en cache
    const cachedUrls = await this.cacheManager.get<string[]>(this.CACHE_KEY);
    if (cachedUrls) {
      this.logger.debug('URLs désactivées récupérées depuis le cache');
      return cachedUrls;
    }

    try {
      const disallowedUrls: string[] = [];
      
      // Articles désactivés
      const disabledArticles = await this.prisma.articleBlog.findMany({
        where: { published: false },
        select: { slug: true }
      });
      disallowedUrls.push(...disabledArticles.map(article => `/blog/${article.slug}`));
      
      // Pages désactivées
      const disabledPages = await this.prisma.page.findMany({
        where: { active: false },
        select: { slug: true }
      });
      disallowedUrls.push(...disabledPages.map(page => `/page/${page.slug}`));
      
      // URLs supprimées (410 Gone)
      const goneUrls = await this.prisma.goneUrl.findMany({
        select: { path: true }
      });
      disallowedUrls.push(...goneUrls.map(url => url.path));

      // Mettre en cache pour 24h
      await this.cacheManager.set(this.CACHE_KEY, disallowedUrls, this.CACHE_TTL * 1000);
      
      return disallowedUrls;
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération des URLs désactivées: ${error.message}`, error.stack);
      return [];
    }
  }

  /**
   * Ajoute une URL à la liste des URLs désactivées
   */
  async addDisallowedUrl(path: string, reason?: string): Promise<void> {
    try {
      await this.prisma.goneUrl.upsert({
        where: { path },
        update: { reason: reason || 'Contenu supprimé' },
        create: {
          path,
          reason: reason || 'Contenu supprimé',
          removedAt: new Date()
        }
      });
      
      // Invalider le cache des URLs désactivées
      await this.invalidateCache();
    } catch (error) {
      this.logger.error(`Erreur lors de l'ajout de l'URL désactivée: ${error.message}`, error.stack);
    }
  }

  /**
   * Invalide le cache des URLs désactivées lors d'une modification du contenu
   */
  @OnEvent('content.deleted')
  @OnEvent('content.updated')
  async handleContentChange() {
    this.logger.log('Modification de contenu détectée, invalidation du cache des URLs désactivées');
    await this.invalidateCache();
  }

  /**
   * Nettoie le cache des URLs désactivées
   */
  private async invalidateCache(): Promise<void> {
    try {
      await this.cacheManager.del(this.CACHE_KEY);
      this.logger.log('Cache des URLs désactivées invalidé');
    } catch (error) {
      this.logger.error(`Erreur lors de l'invalidation du cache: ${error.message}`, error.stack);
    }
  }
}
