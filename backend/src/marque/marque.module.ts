import { Module } from '@nestjs/common';
import { MarqueController } from './marque.controller';
import { MarqueService } from './marque.service';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  controllers: [MarqueController],
  providers: [MarqueService, PrismaService],
  exports: [MarqueService],
})
export class MarqueModule {}
