import { Module } from '@nestjs/common';
import { OrdersService } from './orders.service';
import { OrdersController } from './orders.controller';
import { InvoiceController } from './invoice.controller';
import { PrismaService } from '../prisma/prisma.service';

@Module({
  controllers: [OrdersController, InvoiceController], // Ajout du nouveau contrôleur
  providers: [OrdersService, PrismaService],
  exports: [OrdersService],
})
export class OrdersModule {}
