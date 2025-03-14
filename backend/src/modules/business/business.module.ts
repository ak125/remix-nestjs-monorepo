import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { OrdersModule } from '../orders/orders.module';
import { CartModule } from '../cart/cart.module';
import { OrderArchiveModule } from '../order-archive/order-archive.module';
import { CancelOrderModule } from '../orders/modules/cancel-order.module';
import { OrderStatusModule } from '../orders/modules/order-status.module';
import { PrismaService } from '../../prisma/prisma.service';

@Module({
  imports: [
    ConfigModule,
    // Modules métier
    OrdersModule,
    CartModule,
    OrderArchiveModule,
    CancelOrderModule,
    OrderStatusModule, // Ajout du module de gestion des statuts
  ],
  providers: [PrismaService],
  exports: [
    // Modules métier publics
    OrdersModule,
    CartModule,
    OrderArchiveModule,
    CancelOrderModule,
    OrderStatusModule, // Export du module de gestion des statuts
  ],
})
export class BusinessModule {}
