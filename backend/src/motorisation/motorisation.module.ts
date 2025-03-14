import { Module } from '@nestjs/common';
import { MotorisationController } from './controllers/motorisation.controller';
import { MotorisationService } from './services/motorisation.service';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  controllers: [MotorisationController],
  providers: [
    MotorisationService,
    PrismaService
  ],
  exports: [MotorisationService]
})
export class MotorisationModule {}
