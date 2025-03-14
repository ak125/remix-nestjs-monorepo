import { Module } from '@nestjs/common';
import { CompositController } from './controllers/composit.controller';
import { CompositService } from './services/composit.service';
import { PrismaService } from '../../prisma/prisma.service';

@Module({
  controllers: [CompositController],
  providers: [CompositService, PrismaService],
})
export class CompositModule {}
