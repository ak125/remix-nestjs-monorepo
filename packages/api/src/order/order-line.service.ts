import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { z } from 'zod';

const updateLineSchema = z.object({
  status: z.enum(['received', 'deleted']),
  reason: z.string().optional(),
  userId: z.string()
});

@Injectable()
export class OrderLineService {
  constructor(private prisma: PrismaService) {}

  async updateLineStatus(orderId: string, lineId: string, data: unknown) {
    const validated = updateLineSchema.parse(data);
    const line = await this.getOrderLine(orderId, lineId);

    const newStatus = validated.status === 'received' ? 11 : 12;

    return this.prisma.$transaction(async (tx) => {
      // Update line status
      const updated = await tx.orderLine.update({
        where: { id: lineId },
        data: {
          status: String(newStatus),
          isReceived: validated.status === 'received',
          receivedAt: validated.status === 'received' ? new Date() : null,
          history: {
            create: {
              status: validated.status,
              reason: validated.reason,
              userId: validated.userId
            }
          }
        }
      });

      // Check if all lines are processed
      const remainingLines = await tx.orderLine.count({
        where: {
          orderId,
          status: 'pending'
        }
      });

      if (remainingLines === 0) {
        await tx.order.update({
          where: { id: orderId },
          data: { status: 'completed' }
        });
      }

      return updated;
    });
  }

  private async getOrderLine(orderId: string, lineId: string) {
    const line = await this.prisma.orderLine.findFirst({
      where: {
        id: lineId,
        orderId
      }
    });

    if (!line) {
      throw new NotFoundException(`Order line ${lineId} not found`);
    }

    return line;
  }
}
