import { Module } from '@nestjs/common';
import { MineController } from './controllers/mine.controller';
import { MineService } from './services/mine.service';
import { PrismaModule } from '../../prisma/prisma.module';
import { ContentModule } from '../../common/content.module';

@Module({
  imports: [
    PrismaModule,
    ContentModule,
  ],
  controllers: [MineController],
  providers: [MineService],
  exports: [MineService]
})
export class MineModule {}
