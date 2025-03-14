import { Module, CacheModule } from '@nestjs/common';
import { ModelsController } from './controllers/models.controller';
import { ModelsService } from './services/models.service';
import { PrismaModule } from '../../prisma/prisma.module';

@Module({
  imports: [
    PrismaModule,
    CacheModule.register({
      ttl: 3600, // 1 heure
      max: 100 // 100 items max
    })
  ],
  controllers: [ModelsController],
  providers: [ModelsService],
  exports: [ModelsService]
})
export class ModelsModule {}
