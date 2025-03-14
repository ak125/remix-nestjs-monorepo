import { Injectable, ConflictException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { z } from 'zod';

const createLinkSchema = z.object({
  supplierId: z.string(),
  brandId: z.string(),
  userId: z.string()
});

@Injectable()
export class SupplierLinkService {
  constructor(
    private prisma: PrismaService,
    private eventEmitter: EventEmitter2
  ) {}

  async linkSupplierToBrand(data: unknown) {
    const validated = createLinkSchema.parse(data);

    return this.prisma.$transaction(async (tx) => {
      // Check if link already exists
      const existing = await tx.manufacturerLink.findUnique({
        where: {
          supplierId_manufacturerId: {
            supplierId: validated.supplierId,
            manufacturerId: validated.brandId
          }
        }
      });

      if (existing) {
        throw new ConflictException('Link already exists');
      }

      // Create link
      const link = await tx.manufacturerLink.create({
        data: {
          supplierId: validated.supplierId,
          manufacturerId: validated.brandId,
          isActive: true
        },
        include: {
          supplier: true,
          manufacturer: true
        }
      });

      // Log action
      await tx.supplierHistory.create({
        data: {
          supplierId: validated.supplierId,
          action: 'linked',
          userId: validated.userId,
          details: {
            manufacturerId: validated.brandId,
            manufacturerName: link.manufacturer.name
          }
        }
      });

      // Emit event
      this.eventEmitter.emit('supplier.linked', {
        supplierId: validated.supplierId,
        manufacturerId: validated.brandId,
        userId: validated.userId
      });

      return link;
    });
  }
}
