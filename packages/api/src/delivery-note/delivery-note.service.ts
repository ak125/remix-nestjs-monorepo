import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { PDFService } from '../pdf/pdf.service';
import { NotificationService } from '../notification/notification.service';
import { z } from 'zod';

const generateDeliveryNoteSchema = z.object({
  orderId: z.string(),
  paymentStatus: z.boolean(),
  userId: z.string()
});

@Injectable()
export class DeliveryNoteService {
  constructor(
    private prisma: PrismaService,
    private pdf: PDFService,
    private notification: NotificationService
  ) {}

  async generateDeliveryNote(data: unknown) {
    const validated = generateDeliveryNoteSchema.parse(data);

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

      // Create delivery note
      const deliveryNote = await tx.deliveryNote.create({
        data: {
          orderId: order.id,
          clientId: order.clientId,
          total: order.total,
          paymentStatus: validated.paymentStatus,
          items: {
            create: order.items.map(item => ({
              productId: item.productId,
              quantity: item.quantity,
              unitPrice: item.unitPrice,
              totalPrice: item.totalPrice
            }))
          },
          history: {
            create: {
              action: 'created',
              userId: validated.userId
            }
          }
        }
      });

      // Generate PDF
      const pdfBuffer = await this.pdf.generateDeliveryNote(deliveryNote);

      // Save document
      await tx.document.create({
        data: {
          type: 'delivery_note',
          filename: `bl_${deliveryNote.id}.pdf`,
          content: pdfBuffer,
          deliveryNoteId: deliveryNote.id
        }
      });

      // Send notification
      await this.notification.sendDeliveryNoteGenerated(
        order.customer.email,
        deliveryNote
      );

      return deliveryNote;
    });
  }

  async updatePaymentStatus(id: string, paymentStatus: boolean, userId: string) {
    return this.prisma.deliveryNote.update({
      where: { id },
      data: {
        paymentStatus,
        history: {
          create: {
            action: 'payment_status_updated',
            userId,
            details: { paymentStatus }
          }
        }
      }
    });
  }
}
