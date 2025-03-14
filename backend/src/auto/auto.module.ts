import { Module } from '@nestjs/common';
import { AutoController } from './auto.controller';
import { AutoService } from './auto.service';
import { PrismaService } from '../prisma/prisma.service';
import { ReviewsService } from './reviews.service';
import { ComparisonService } from './comparison.service';
import { ChatbotService } from './chatbot.service';
import { AnalyticsService } from './analytics.service';

@Module({
  controllers: [AutoController],
  providers: [
    AutoService,
    PrismaService,
    ReviewsService,
    ComparisonService, 
    ChatbotService,
    AnalyticsService
  ],
  exports: [AutoService],
})
export class AutoModule {}
