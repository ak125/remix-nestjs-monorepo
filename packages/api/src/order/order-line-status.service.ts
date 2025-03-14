import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { z } from 'zod';

const updateStatusSchema = z.object({
  lineId: z.string(),
  orderId: z.string(),
  status: z.enum(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
  userId: z.string()
});

@Injectable()
export class OrderLineStatusService {
  constructor(
    private prisma: PrismaService,
    private eventEmitter: EventEmitter2
  ) {}

  async updateStatus(data: unknown) {
    const validated = updateStatusSchema.parse(data);

    return this.prisma.$transaction(async (tx) => {
      // Get current line status
      const line = await tx.orderLine.findFirst({
        where: {
          id: validated.lineId,
          orderId: validated.orderId
        },
        include: {
          order: {
            include: {
              client: true
            }
          }
        }
      });

      if (!line) {
        throw new NotFoundException('Order line not found');
      }

      // Update line status
      const updated = await tx.orderLine.update({
        where: { id: validated.lineId },
        data: {
          status: validated.status,
          history: {
            create: {
              status: validated.status,
              userId: validated.userId
            }
          }
        }
      });

      // Create notification
      await tx.notification.create({
        data: {
          type: 'order_line_status',
          title: 'Statut mis à jour',
          message: `Commande #${line.order.orderNumber}: Article ${line.id} passé à "${validated.status}"`,
          userId: line.order.clientId
        }
      });

      // Log action
      await tx.actionLog.create({
        data: {
          type: 'order_line_status_updated',
          details: {
            orderId: validated.orderId,
            lineId: validated.lineId,
            oldStatus: line.status,
            newStatus: validated.status
          },
          userId: validated.userId
        }
      });

      // Emit event
      this.eventEmitter.emit('order.line.status_updated', {
        orderId: validated.orderId,
        lineId: validated.lineId,
        status: validated.status,
        userId: validated.userId
      });

      return updated;
    });
  }
}
