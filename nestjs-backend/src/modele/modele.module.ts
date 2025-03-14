import { Module } from '@nestjs/common';
import { ModeleController } from './modele.controller';
import { ModeleService } from './modele.service';
import { PrismaModule } from '../prisma/prisma.module';
import { CacheModule } from '@nestjs/cache-manager';
import { ConfigModule, ConfigService } from '@nestjs/config';
import * as redisStore from 'cache-manager-redis-store';

@Module({
  imports: [
    PrismaModule,
    CacheModule.registerAsync({
      imports: [ConfigModule],
      inject: [ConfigService],
      useFactory: (configService: ConfigService) => ({
        store: redisStore,
        host: configService.get('REDIS_HOST', 'localhost'),
        port: configService.get('REDIS_PORT', 6379),
        ttl: 600, // 10 minutes par défaut
        max: 1000, // nombre maximum d'éléments en cache
      }),
    }),
  ],
  controllers: [ModeleController],
  providers: [ModeleService],
  exports: [ModeleService],
})
export class ModeleModule {}
