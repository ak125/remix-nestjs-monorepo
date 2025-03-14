import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { PDFService } from '../pdf/pdf.service';
import { z } from 'zod';

const generateBLSchema = z.object({
  orderId: z.string(),
  isPaid: z.boolean().default(false),
  userId: z.string()
});

@Injectable()
export class DeliveryService {
  constructor(
    private prisma: PrismaService,
    private pdf: PDFService
  ) {}

  async generateBL(data: unknown) {
    const validated = generateBLSchema.parse(data);

    return this.prisma.$transaction(async (tx) => {
      const order = await tx.order.findUnique({
        where: { id: validated.orderId },
        include: {
          items: true,
          customer: true
        }
      });

      if (!order) {
        throw new NotFoundException('Commande non trouvée');
      }

      // Create delivery record
      const delivery = await tx.delivery.create({
        data: {
          orderId: order.id,
          status: validated.isPaid ? 'ready' : 'pending_payment',
          deliveryNumber: `BL${order.orderNumber}`,
          history: {
            create: {
              action: 'created',
              userId: validated.userId,
              details: { isPaid: validated.isPaid }
            }
          }
        }
      });

      // Generate PDF
      const pdfBuffer = await this.pdf.generateDeliveryNote({
        delivery,
        order,
        isPaid: validated.isPaid
      });

      // Save PDF
      await tx.document.create({
        data: {
          type: 'delivery_note',
          filename: `bl_${delivery.deliveryNumber}.pdf`,
          content: pdfBuffer,
          deliveryId: delivery.id
        }
      });

      return delivery;
    });
  }

  async updateDeliveryStatus(id: string, status: string, userId: string) {
    const delivery = await this.prisma.delivery.findUnique({
      where: { id }
    });

    if (!delivery) {
      throw new NotFoundException(`Delivery ${id} not found`);
    }

    return this.prisma.delivery.update({
      where: { id },
      data: {
        status,
        history: {
          create: {
            action: 'status_updated',
            userId,
            details: { 
              oldStatus: delivery.status,
              newStatus: status
            }
          }
        }
      }
    });
  }
}
