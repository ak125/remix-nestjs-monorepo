import { Module } from '@nestjs/common';
import { AutoController } from './controllers/auto.controller';
import { AutoService } from './services/auto.service';
import { VehicleService } from './services/vehicle.service';
import { PartsService } from './services/parts.service';
import { PrismaService } from '../../prisma/prisma.service';

@Module({
  controllers: [AutoController],
  providers: [
    AutoService, 
    VehicleService,
    PartsService,
    PrismaService,
  ],
  exports: [AutoService],
})
export class AutoModule {}
