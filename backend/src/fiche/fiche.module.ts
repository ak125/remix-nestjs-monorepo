import { Module } from '@nestjs/common';
import { FicheController } from './fiche.controller';
import { FicheService } from './fiche.service';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  controllers: [FicheController],
  providers: [
    FicheService,
    PrismaService
  ],
  exports: [FicheService]
})
export class FicheModule {}
