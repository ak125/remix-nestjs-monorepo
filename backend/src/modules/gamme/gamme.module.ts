import { Module } from '@nestjs/common';
import { GammeController } from './controllers/gamme.controller';
import { GammeService } from './services/gamme.service';
import { PrismaModule } from '../../prisma/prisma.module';
import { ContentModule } from '../../common/content.module';
import { CacheModule } from '@nestjs/cache-manager';

@Module({
  imports: [
    PrismaModule,
    ContentModule,
    CacheModule.register({
      ttl: 60 * 60, // 1 heure
      max: 100 // 100 items max
    })
  ],
  controllers: [GammeController],
  providers: [GammeService],
  exports: [GammeService]
})
export class GammeModule {}
