import { Controller, Get, Logger, Post, Body, UseGuards } from '@nestjs/common';
import { RobotsService } from './robots.service';
import { DisabledUrlsService } from './disabled-urls.service';
import { AdminAuthGuard } from '../auth/guards/admin-auth.guard';
import { BotDetectionService } from './bot-detection.service';

@Controller('api/robots')
export class RobotsController {
  private readonly logger = new Logger(RobotsController.name);

  constructor(
    private readonly robotsService: RobotsService,
    private readonly botDetectionService: BotDetectionService,
    private readonly disabledUrlsService: DisabledUrlsService
  ) {}

  @Get('config')
  async getRobotsConfig() {
    this.logger.log('Récupération de la configuration robots.txt');
    
    try {
      const rules = await this.robotsService.getRules();
      const blockedBots = await this.botDetectionService.getBlockedBots();
      
      // Ajouter les règles pour bloquer les bots indésirables
      blockedBots.forEach(bot => {
        rules.push({
          userAgent: bot,
          disallow: ['/']
        });
      });
      
      const sitemaps = await this.robotsService.getSitemaps();
      const crawlDelay = await this.robotsService.getCrawlDelay();
      const host = await this.robotsService.getHost();
      
      return {
        rules,
        sitemaps,
        crawlDelay,
        host
      };
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération de la configuration: ${error.message}`, error.stack);
      throw error;
    }
  }

  @Post('config')
  @UseGuards(AdminAuthGuard)
  async updateRobotsConfig(@Body() config: any) {
    this.logger.log('Mise à jour de la configuration robots.txt');
    
    try {
      await this.robotsService.updateConfiguration(config);
      return { success: true, message: 'Configuration mise à jour avec succès' };
    } catch (error) {
      this.logger.error(`Erreur lors de la mise à jour de la configuration: ${error.message}`, error.stack);
      throw error;
    }
  }

  @Get('blocked-bots')
  @UseGuards(AdminAuthGuard)
  async getBlockedBots() {
    return {
      blockedBots: await this.botDetectionService.getBlockedBots()
    };
  }

  @Get('disallowed-urls')
  async getDisallowedUrls() {
    this.logger.log('Récupération des URLs désactivées pour robots.txt');
    
    try {
      const urls = await this.disabledUrlsService.getDisallowedUrls();
      return { urls };
    } catch (error) {
      this.logger.error(`Erreur lors de la récupération des URLs désactivées: ${error.message}`, error.stack);
      return { urls: [] };
    }
  }

  @Post('disallowed-urls')
  @UseGuards(AdminAuthGuard)
  async addDisallowedUrl(@Body() data: { path: string, reason?: string }) {
    this.logger.log(`Ajout d'une URL désactivée: ${data.path}`);
    
    try {
      await this.disabledUrlsService.addDisallowedUrl(data.path, data.reason);
      return { success: true, message: 'URL ajoutée avec succès' };
    } catch (error) {
      this.logger.error(`Erreur lors de l'ajout de l'URL désactivée: ${error.message}`, error.stack);
      return { success: false, message: 'Erreur lors de l\'ajout de l\'URL' };
    }
  }
}
