import { Module } from '@nestjs/common';
import { RobotsController } from './robots.controller';
import { BotsController } from './bots.controller'; 
import { RobotsService } from './robots.service';
import { BotDetectionService } from './bot-detection.service';
import { DisabledUrlsService } from './disabled-urls.service';
import { PrismaModule } from '../prisma/prisma.module';
import { ScheduleModule } from '@nestjs/schedule';

@Module({
  imports: [
    PrismaModule,
    ScheduleModule.forRoot()
  ],
  controllers: [RobotsController, BotsController],
  providers: [RobotsService, BotDetectionService, DisabledUrlsService],
  exports: [RobotsService, BotDetectionService, DisabledUrlsService]
})
export class RobotsModule {}
