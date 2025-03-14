import { Module, Global } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { EventEmitterModule } from '@nestjs/event-emitter';
import { PrismaModule } from '../prisma/prisma.module';
import { LoggerService } from '../common/services/logger.service';
import { AuthModule } from '../auth/auth.module';
import { OrdersModule } from '../orders/orders.module';
import { CartModule } from '../modules/cart/cart.module'; // Ajout du CartModule
import { InvoiceService } from './invoice.service';
import { InvoiceController } from './invoice.controller';

@Global()
@Module({
  imports: [
    AuthModule, 
    OrdersModule, 
    CartModule,  // Ajout du module Cart ici
    PrismaModule, // Utilisation du module Prisma

    // Configuration globale
    ConfigModule.forRoot({
      isGlobal: true,
      cache: true,
    }),

    // Events
    EventEmitterModule.forRoot({
      wildcard: false,
      delimiter: '.',
      maxListeners: 10,
      verboseMemoryLeak: process.env.NODE_ENV === 'development',
    }),
  ],
  
  controllers: [InvoiceController], 
  providers: [
    InvoiceService, // PrismaService est géré par PrismaModule
    LoggerService,
  ],
  
  exports: [
    InvoiceService, // Exporte uniquement le service de facturation
    PrismaModule,
    ConfigModule,
    EventEmitterModule,
    LoggerService,
  ],
})
export class CoreModule {}
