import { Module } from '@nestjs/common';
import { OrderArchiveService } from './order-archive.service';
import { OrderArchiveController } from './order-archive.controller';
import { PrismaService } from '../../prisma/prisma.service';
import { ConfigModule } from '@nestjs/config';

@Module({
  imports: [ConfigModule],
  controllers: [OrderArchiveController],
  providers: [OrderArchiveService, PrismaService],
  exports: [OrderArchiveService],
})
export class OrderArchiveModule {}
