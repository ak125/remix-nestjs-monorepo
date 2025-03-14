import { Module, MiddlewareConsumer, RequestMethod } from '@nestjs/common';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { PrismaModule } from './prisma/prisma.module';
import { GammeModule } from './gamme/gamme.module';
import { ModeleModule } from './modele/modele.module';
import { AuthModule } from './auth/auth.module';
import { ConfigModule } from '@nestjs/config';
import { RobotsModule } from './robots/robots.module';
import { SitemapModule } from './sitemap/sitemap.module';
import { EventEmitterModule } from '@nestjs/event-emitter';

@Module({
  imports: [
    ConfigModule.forRoot({ isGlobal: true }),
    EventEmitterModule.forRoot(), // Ajout du module d'événements
    PrismaModule,
    GammeModule,
    ModeleModule,
    AuthModule,
    RobotsModule,
    SitemapModule
  ],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
