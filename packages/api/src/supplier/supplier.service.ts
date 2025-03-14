import { Injectable, NotFoundException, ConflictException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { createSlug } from '../utils/string';
import { z } from 'zod';

const supplierSchema = z.object({
  name: z.string().min(2),
  isActive: z.boolean().default(true)
});

@Injectable()
export class SupplierService {
  constructor(private prisma: PrismaService) {}

  async createSupplier(data: unknown, userId: string) {
    const validated = supplierSchema.parse(data);
    const alias = createSlug(validated.name);

    const existing = await this.prisma.supplier.findUnique({
      where: { name: validated.name }
    });

    if (existing) {
      throw new ConflictException('Ce nom de fournisseur existe déjà');
    }

    return this.prisma.prisma.$transaction(async (tx) => {
      const supplier = await tx.supplier.create({
        data: {
          ...validated,
          alias,
          history: {
            create: {
              action: 'created',
              details: validated,
              userId
            }
          }
        }
      });

      await this.createAuditLog(tx, supplier.id, 'created', userId);

      return supplier;
    });
  }

  async updateSupplier(id: string, data: unknown, userId: string) {
    const validated = supplierSchema.parse(data);
    const supplier = await this.getSupplier(id);

    return this.prisma.prisma.$transaction(async (tx) => {
      const updated = await tx.supplier.update({
        where: { id },
        data: {
          ...validated,
          history: {
            create: {
              action: 'updated',
              details: validated,
              userId
            }
          }
        }
      });

      await this.createAuditLog(tx, id, 'updated', userId);

      return updated;
    });
  }

  async linkManufacturer(supplierId: string, manufacturerId: string, userId: string) {
    const supplier = await this.getSupplier(supplierId);
    const manufacturer = await this.prisma.manufacturer.findUnique({
      where: { id: manufacturerId }
    });

    if (!manufacturer) {
      throw new NotFoundException('Manufacturer not found');
    }

    const link = await this.prisma.manufacturerLink.create({
      data: {
        supplierId,
        manufacturerId,
        supplierHistory: {
          create: {
            action: 'linked',
            details: { manufacturerId, manufacturerName: manufacturer.name },
            userId,
            supplierId
          }
        }
      },
      include: {
        manufacturer: true
      }
    });

    return link;
  }

  async getSupplierLinks(supplierId: string) {
    const supplier = await this.getSupplier(supplierId);

    return this.prisma.manufacturerLink.findMany({
      where: { 
        supplierId,
        isActive: true
      },
      include: {
        manufacturer: true
      },
      orderBy: {
        manufacturer: {
          name: 'asc'
        }
      }
    });
  }

  private async createAuditLog(tx: any, supplierId: string, action: string, userId: string) {
    return tx.supplierAudit.create({
      data: {
        supplierId,
        action,
        userId,
        timestamp: new Date()
      }
    });
  }

  private async getSupplier(id: string) {
    const supplier = await this.prisma.supplier.findUnique({
      where: { id }
    });

    if (!supplier) {
      throw new NotFoundException(`Fournisseur ${id} non trouvé`);
    }

    return supplier;
  }
}
