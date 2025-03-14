import { Module, CacheModule } from '@nestjs/common';
import { ModeleController } from './controllers/modele.controller';
import { ModeleService } from './services/modele.service';
import { PrismaModule } from '../../prisma/prisma.module';

@Module({
  imports: [
    PrismaModule,
    CacheModule.register({
      ttl: 3600,
      max: 100
    })
  ],
  controllers: [ModeleController],
  providers: [ModeleService],
  exports: [ModeleService]
})
export class ModeleModule {}
