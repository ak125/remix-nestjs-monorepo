import { Module } from '@nestjs/common';
import { CartController } from './controllers/cart.controller';
import { CartService } from './services/cart.service';
import { PrismaService } from '../../prisma/prisma.service';
import { LoggerService } from '../../common/services/logger.service';
import { EventEmitterModule } from '@nestjs/event-emitter';
import { RedisModule } from '@nestjs/redis';
import { ConfigModule, ConfigService } from '@nestjs/config';
import * as RedisStore from 'connect-redis';
import { Redis } from 'ioredis';
import * as session from 'express-session';
import * as cookieParser from 'cookie-parser';
import helmet from 'helmet';
import { DocumentService } from './services/document.service';
import { PrintService } from './services/print.service';
import { SharedModule } from '../shared/shared.module';

@Module({
  imports: [
    SharedModule,
    // Redis pour le stockage panier
    RedisModule.forRootAsync({
      imports: [ConfigModule],
      useFactory: async (configService: ConfigService) => ({
        config: {
          host: configService.get('REDIS_HOST', 'localhost'),
          port: configService.get('REDIS_PORT', 6379),
          password: configService.get('REDIS_PASSWORD'),
          keyPrefix: 'cart:',
        },
      }),
      inject: [ConfigService],
    }),

    // Events pour notifications temps réel
    EventEmitterModule.forRoot({
      wildcard: false,
      delimiter: '.',
      maxListeners: 10,
      verboseMemoryLeak: process.env.NODE_ENV === 'development',
    }),

    // Configuration globale
    ConfigModule.forRoot({
      isGlobal: true,
      cache: true,
    }),
  ],

  controllers: [CartController],

  providers: [
    CartService,
    PrismaService, 
    LoggerService,

    // TTL du panier
    {
      provide: 'CART_TTL',
      useValue: 60 * 60 * 24 * 7, // 1 semaine
    },

    // Prix des impressions
    {
      provide: 'PRICE_CONFIG',
      useValue: {
        formats: {
          A4: { simple: 0.15, recto_verso: 0.25 },
          A3: { simple: 0.30, recto_verso: 0.50 },
        },
        manuscriptMarkup: 1.5, // +50% pour manuscrits
        copyMarkup: 1.2, // +20% pour photocopies
      },
    },
  ],

  exports: [CartService],
})
export class CartModule {
  static configureApp(app: any, configService: ConfigService) {
    // Configuration Redis
    const redisClient = new Redis({
      host: configService.get('REDIS_HOST', 'localhost'),
      port: configService.get('REDIS_PORT', 6379),
      password: configService.get('REDIS_PASSWORD'),
    });

    // CORS et sécurité
    app.enableCors({
      origin: configService.get('FRONTEND_URL', 'http://localhost:3000'),
      credentials: true,
    });
    app.use(cookieParser());
    app.use(helmet());

    // Configuration session
    app.use(
      session({
        store: new RedisStore({
          client: redisClient,
          prefix: 'sess:',
        }),
        secret: configService.get('SESSION_SECRET', 'your-super-secret-key'),
        resave: false,
        saveUninitialized: false,
        name: 'sessionId',
        cookie: {
          secure: process.env.NODE_ENV === 'production',
          httpOnly: true,
          maxAge: 7 * 24 * 60 * 60 * 1000,
          sameSite: process.env.NODE_ENV === 'production' ? 'strict' : 'lax',
        },
      }),
    );
  }
}
