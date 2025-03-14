import { Module } from '@nestjs/common';
import { SitemapController } from './sitemap.controller';
import { SitemapApiController } from './sitemap-api.controller';
import { SitemapService } from './sitemap.service';
import { PrismaModule } from '../prisma/prisma.module';
import { ConfigModule } from '@nestjs/config';

@Module({
  imports: [
    PrismaModule,
    ConfigModule
  ],
  controllers: [SitemapController, SitemapApiController],
  providers: [SitemapService],
  exports: [SitemapService]
})
export class SitemapModule {}
