import { Module, CacheModule } from '@nestjs/common';
import { MarqueController } from './controllers/marque.controller';
import { MarqueService } from './services/marque.service';
import { PrismaModule } from '../../prisma/prisma.module';
import { ContentModule } from '../../common/content.module';

@Module({
  imports: [
    PrismaModule,
    ContentModule,
    CacheModule.register({
      ttl: 3600, // 1 heure
      max: 50 // 50 marques max
    })
  ],
  controllers: [MarqueController],
  providers: [MarqueService],
  exports: [MarqueService]
})
export class MarqueModule {}
