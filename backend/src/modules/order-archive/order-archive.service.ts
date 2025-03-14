import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class OrderArchiveService {
  constructor(private readonly prisma: PrismaService) {}

  async getArchivedOrders(page = 1, limit = 10) {
    const skip = (page - 1) * limit;

    const [orders, total] = await Promise.all([
      this.prisma.xTR_ORDER_ARCHIVE.findMany({
        skip,
        take: limit,
        orderBy: { order_date: 'desc' },
        include: {
          xTR_CUSTOMER: {
            select: {
              CST_ID: true,
              CST_MAIL: true,
              CST_NAME: true,
              CST_FNAME: true,
            },
          },
          xTR_ORDER_ARCHIVE_ITEMS: {
            include: {
              xTR_PIECE: true,
            },
          },
        },
      }),
      this.prisma.xTR_ORDER_ARCHIVE.count(),
    ]);

    return {
      orders,
      pagination: {
        total,
        pages: Math.ceil(total / limit),
        current: page,
        limit,
      },
    };
  }

  async getArchivedOrderById(orderId: string) {
    const order = await this.prisma.xTR_ORDER_ARCHIVE.findUnique({
      where: { order_id: orderId },
      include: {
        xTR_CUSTOMER: {
          select: {
            CST_ID: true,
            CST_MAIL: true,
            CST_NAME: true,
            CST_FNAME: true,
          },
        },
        xTR_ORDER_ARCHIVE_ITEMS: {
          include: {
            xTR_PIECE: true,
          },
        },
        xTR_CUSTOMER_BILLING_ADDRESS: true,
        xTR_CUSTOMER_DELIVERY_ADDRESS: true,
      },
    });

    if (!order) {
      throw new NotFoundException('Commande archivée non trouvée');
    }

    return order;
  }

  async archiveOrder(orderId: string) {
    // Récupérer la commande originale
    const order = await this.prisma.xTR_ORDER.findUnique({
      where: { order_id: orderId },
      include: {
        xTR_ORDER_ITEMS: true,
      },
    });

    if (!order) {
      throw new NotFoundException('Commande non trouvée');
    }

    // Créer l'archive avec transaction
    await this.prisma.$transaction([
      // Créer l'archive de la commande
      this.prisma.xTR_ORDER_ARCHIVE.create({
        data: {
          order_id: order.order_id,
          order_cst_id: order.order_cst_id,
          order_date: order.order_date,
          order_total: order.order_total,
          order_status: 'archived',
          // ... autres champs
        },
      }),
      // Archiver les items
      ...order.xTR_ORDER_ITEMS.map(item =>
        this.prisma.xTR_ORDER_ARCHIVE_ITEMS.create({
          data: {
            order_id: orderId,
            piece_id: item.piece_id,
            quantity: item.quantity,
            price: item.price,
          },
        })
      ),
      // Supprimer la commande originale
      this.prisma.xTR_ORDER.delete({
        where: { order_id: orderId },
      }),
    ]);

    return { message: 'Commande archivée avec succès' };
  }
}
