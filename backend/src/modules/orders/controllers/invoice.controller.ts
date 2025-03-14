import { Controller, Get, Query, Req, UnauthorizedException } from '@nestjs/common';
import { OrdersService } from './orders.service';
import { PrismaService } from '../prisma/prisma.service';
import { Request } from 'express';

@Controller('invoices')
export class InvoiceController {
  constructor(
    private readonly ordersService: OrdersService,
    private readonly prisma: PrismaService
  ) {}

  @Get()
  async getInvoice(@Query('com_id') comId: string, @Req() req: Request) {
    // Vérifier si l'utilisateur est authentifié
    if (!req.user) {
      throw new UnauthorizedException('Utilisateur non connecté.');
    }

    const userId = req.user.id; // L'ID de l'utilisateur connecté

    // Récupérer les données de la facture
    const invoice = await this.prisma.invoice.findUnique({
      where: { orderId: comId },
      include: {
        order: true, // Inclure les détails de la commande
        customer: true, // Inclure les infos du client
      },
    });

    if (!invoice) {
      throw new UnauthorizedException('Facture introuvable ou non disponible.');
    }

    return {
      invoiceId: invoice.id,
      orderId: invoice.orderId,
      totalAmount: invoice.totalAmount,
      date: invoice.createdAt,
      customer: {
        name: invoice.customer.name,
        email: invoice.customer.email,
        address: invoice.customer.address,
      },
      items: invoice.order.items.map((item) => ({
        productName: item.productName,
        quantity: item.quantity,
        price: item.price,
      })),
    };
  }
}
