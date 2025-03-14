import { Module, CacheModule } from '@nestjs/common';
import { YearsController } from './controllers/years.controller';
import { YearsService } from './services/years.service';
import { PrismaModule } from '../../prisma/prisma.module';

@Module({
  imports: [
    PrismaModule,
    CacheModule.register({
      ttl: 3600, // 1 heure
      max: 100 // 100 marques max
    })
  ],
  controllers: [YearsController],
  providers: [YearsService],
  exports: [YearsService]
})
export class YearsModule {}
