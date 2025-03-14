import { Controller, Get, Post, Delete, Param, Body, Logger } from '@nestjs/common';
import { BotDetectionService } from './bot-detection.service';

@Controller('api/bots')
export class BotsController {
  private readonly logger = new Logger(BotsController.name);

  constructor(private readonly botService: BotDetectionService) {}

  @Get()
  async getBlockedBots() {
    this.logger.log('Récupération de la liste des bots bloqués');
    const blockedBots = await this.botService.getBlockedBots();
    return { blockedBots };
  }

  @Get('stats')
  async getBotsStats() {
    this.logger.log('Récupération des statistiques des bots');
    const stats = await this.botService.getBotsStats();
    return { stats };
  }

  @Post()
  async addBot(@Body() botData: { name: string, reason?: string }) {
    this.logger.log(`Ajout d'un nouveau bot à bloquer: ${botData.name}`);
    await this.botService.addBlockedBot(botData.name, botData.reason);
    return { success: true, message: `Bot ${botData.name} ajouté avec succès` };
  }

  @Delete(':name')
  async removeBot(@Param('name') name: string) {
    this.logger.log(`Suppression du bot: ${name}`);
    await this.botService.removeBlockedBot(name);
    return { success: true, message: `Bot ${name} supprimé avec succès` };
  }
}
