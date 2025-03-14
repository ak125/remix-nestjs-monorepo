import { Module, Global } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { CacheModule } from '@nestjs/cache-manager';
import { PrismaService } from './prisma/prisma.service';
import { BlogModule } from './blog/blog.module';
import { GammeModule } from './gamme/gamme.module';

@Global()
@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true
    }),
    CacheModule.register({
      isGlobal: true,
      ttl: 60 * 60 * 24
    }),
    BlogModule,
    GammeModule
  ],
  providers: [PrismaService],
  exports: [PrismaService, BlogModule, GammeModule]
})
export class CoreModule {}
