import { Module } from '@nestjs/common';
import { CarSearchController } from './controllers/car-search.controller';
import { CarSearchService } from './services/car-search.service';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  controllers: [CarSearchController],
  providers: [
    CarSearchService,
    PrismaService
  ],
  exports: [CarSearchService]
})
export class CarSearchModule {}
