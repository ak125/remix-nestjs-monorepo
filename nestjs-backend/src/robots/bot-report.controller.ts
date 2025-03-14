import { Controller, Post, Body, Logger } from '@nestjs/common';
import { BotDetectionService } from './bot-detection.service';

interface BotReportDto {
  userAgent: string;
  ipAddress: string;
  path: string;
}

@Controller('api/robots/report')
export class BotReportController {
  private readonly logger = new Logger(BotReportController.name);
  
  constructor(private readonly botDetectionService: BotDetectionService) {}
  
  @Post()
  async reportBot(@Body() reportData: BotReportDto) {
    this.logger.warn(`Bot suspect détecté: ${reportData.userAgent} depuis l'IP ${reportData.ipAddress} sur ${reportData.path}`);
    
    // Enregistrer le bot suspect
    await this.botDetectionService.recordSuspiciousBot(
      reportData.userAgent,
      reportData.ipAddress,
      reportData.path
    );
    
    return { success: true };
  }
}
