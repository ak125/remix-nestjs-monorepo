import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { EmailService } from '../email/email.service';
import { z } from 'zod';

const orderItemSchema = z.object({
  productId: z.string(),
  quantity: z.number().min(1),
  unitPrice: z.number().min(0)
});

const createOrderSchema = z.object({
  clientId: z.string(),
  items: z.array(orderItemSchema),
  notes: z.string().optional()
});

const updateOrderSchema = z.object({
  status: z.enum(['pending', 'paid', 'processing', 'shipped', 'delivered', 'canceled']),
  department: z.enum(['commercial', 'shipping', 'canceled']).optional(),
  notes: z.string().optional(),
  userId: z.string()
});

@Injectable()
export class OrderService {
  constructor(
    private prisma: PrismaService,
    private email: EmailService
  ) {}

  async getOrders(filters?: {
    status?: string;
    department?: string;
    search?: string;
  }) {
    return this.prisma.order.findMany({
      where: {
        AND: [
          filters?.status ? { status: filters.status } : {},
          filters?.department ? { department: filters.department } : {},
          filters?.search ? {
            OR: [
              { orderId: { contains: filters.search } },
              { customer: { email: { contains: filters.search } } }
            ]
          } : {}
        ]
      },
      include: {
        customer: {
          select: {
            name: true,
            email: true
          }
        },
        history: {
          orderBy: { createdAt: 'desc' },
          take: 1
        }
      },
      orderBy: { dateCreated: 'desc' }
    });
  }

  async createOrder(data: unknown) {
    const validated = createOrderSchema.parse(data);
    
    return this.prisma.$transaction(async (tx) => {
      // Generate order number
      const orderNumber = await this.generateOrderNumber();

      // Calculate totals
      const total = validated.items.reduce(
        (sum, item) => sum + item.quantity * item.unitPrice, 
        0
      );

      // Create order
      const order = await tx.order.create({
        data: {
          clientId: validated.clientId,
          orderNumber,
          total,
          notes: validated.notes,
          items: {
            create: validated.items.map(item => ({
              productId: item.productId,
              quantity: item.quantity,
              unitPrice: item.unitPrice,
              totalPrice: item.quantity * item.unitPrice
            }))
          }
        },
        include: {
          items: true,
          client: {
            select: {
              email: true
            }
          }
        }
      });

      // Generate BL if needed
      if (order.status === 'paid') {
        await this.generateBL(tx, order.id);
      }

      // Update stock levels
      for (const item of validated.items) {
        await tx.stock.update({
          where: { id: item.productId },
          data: {
            quantity: {
              decrement: item.quantity
            }
          }
        });
      }

      // Send confirmation email
      await this.email.sendOrderConfirmation(
        order.client.email,
        order
      );

      return order;
    });
  }

  private async generateOrderNumber(): Promise<string> {
    const date = new Date();
    const prefix = date.getFullYear().toString().slice(-2);
    
    const lastOrder = await this.prisma.order.findFirst({
      where: {
        orderNumber: {
          startsWith: prefix
        }
      },
      orderBy: {
        orderNumber: 'desc'
      }
    });

    const sequence = lastOrder 
      ? parseInt(lastOrder.orderNumber.slice(-5)) + 1
      : 1;

    return `${prefix}${sequence.toString().padStart(5, '0')}`;
  }

  private async generateBL(tx: any, orderId: string) {
    const delivery = await tx.delivery.create({
      data: {
        orderId,
        status: 'pending',
        documents: {
          create: {
            type: 'delivery_note',
            filename: `bl_${orderId}.pdf`
          }
        }
      }
    });

    await this.email.sendDeliveryCreated(delivery);
    
    return delivery;
  }

  async updateOrder(id: string, data: unknown) {
    const validated = updateOrderSchema.parse(data);
    const order = await this.getOrder(id);

    const updated = await this.prisma.order.update({
      where: { id },
      data: {
        status: validated.status,
        department: validated.department,
        history: {
          create: {
            status: validated.status,
            department: validated.department || order.department,
            notes: validated.notes,
            userId: validated.userId
          }
        }
      },
      include: {
        customer: true
      }
    });

    // Send notifications
    if (validated.status === 'shipped') {
      await this.email.sendOrderShipped(
        updated.customer.email,
        updated.orderId
      );
    }

    return updated;
  }

  async updateOrderStatus(id: string, status: string, userId: string) {
    const order = await this.prisma.order.findUnique({
      where: { id },
      include: { 
        client: {
          select: {
            email: true
          }
        }
      }
    });

    if (!order) {
      throw new NotFoundException(`Order ${id} not found`);
    }

    return this.prisma.$transaction(async (tx) => {
      // Update order status
      const updated = await tx.order.update({
        where: { id },
        data: {
          status,
          history: {
            create: {
              action: 'status_updated',
              details: { oldStatus: order.status, newStatus: status },
              userId
            }
          }
        }
      });

      // Create notification
      await tx.notification.create({
        data: {
          type: 'order_status_changed',
          recipient: order.client.email,
          content: `Your order status has been updated to ${status}`
        }
      });

      return updated;
    });
  }

  private async getOrder(id: string) {
    const order = await this.prisma.order.findUnique({
      where: { id }
    });

    if (!order) {
      throw new NotFoundException(`Order ${id} not found`);
    }

    return order;
  }
}
