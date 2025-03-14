import { Controller, Post, UseGuards, Logger, Get } from '@nestjs/common';
import { SitemapService } from './sitemap.service';
import { AdminAuthGuard } from '../auth/guards/admin-auth.guard';

@Controller('api/sitemap')
export class SitemapApiController {
  private readonly logger = new Logger(SitemapApiController.name);
  
  constructor(private readonly sitemapService: SitemapService) {}
  
  @Post('clear-cache')
  @UseGuards(AdminAuthGuard)
  async clearCache() {
    this.logger.log('Demande d\'invalidation du cache des sitemaps');
    
    try {
      await this.sitemapService.invalidateSitemapCache();
      return { success: true, message: 'Cache des sitemaps invalidé avec succès' };
    } catch (error) {
      this.logger.error(`Erreur lors de l'invalidation du cache: ${error.message}`, error.stack);
      return { success: false, message: 'Erreur lors de l\'invalidation du cache' };
    }
  }
  
  @Post('notify')
  @UseGuards(AdminAuthGuard)
  async notifySearchEngines() {
    this.logger.log('Demande de notification des moteurs de recherche');
    
    try {
      await this.sitemapService.notifySearchEngines();
      return { 
        success: true, 
        message: 'Moteurs de recherche notifiés avec succès' 
      };
    } catch (error) {
      this.logger.error(`Erreur lors de la notification: ${error.message}`, error.stack);
      return { 
        success: false, 
        message: 'Erreur lors de la notification des moteurs de recherche' 
      };
    }
  }
  
  @Post('refresh-and-notify')
  @UseGuards(AdminAuthGuard)
  async refreshAndNotify() {
    this.logger.log('Demande de rafraîchissement du sitemap et notification');
    
    try {
      const success = await this.sitemapService.refreshSitemapAndNotify();
      if (success) {
        return { 
          success: true, 
          message: 'Sitemap rafraîchi et moteurs de recherche notifiés avec succès' 
        };
      } else {
        return { 
          success: false, 
          message: 'Erreur lors du rafraîchissement du sitemap' 
        };
      }
    } catch (error) {
      this.logger.error(`Erreur lors du rafraîchissement: ${error.message}`, error.stack);
      return { 
        success: false, 
        message: 'Erreur lors du rafraîchissement du sitemap et de la notification' 
      };
    }
  }
}
