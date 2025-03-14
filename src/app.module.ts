import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { ScheduleModule } from '@nestjs/schedule';
import { AuthModule } from './auth/auth.module';
import { SitemapModule } from './sitemap/sitemap.module';
import { PrismaModule } from './prisma/prisma.module';
import { validate } from './config/env.validation';

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      validate,
      envFilePath: ['.env.local', '.env']
    }),
    ScheduleModule.forRoot(),
    PrismaModule,
    AuthModule,
    SitemapModule
  ]
})
export class AppModule {}
