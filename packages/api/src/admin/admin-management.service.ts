import { Injectable, ConflictException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { HashService } from '../hash/hash.service';
import { z } from 'zod';

const createAdminSchema = z.object({
  login: z.string().min(3),
  email: z.string().email(),
  password: z.string().min(8),
  firstName: z.string(),
  lastName: z.string(),
  phone: z.string().optional(),
  role: z.enum(['COMMERCIAL', 'SHIPPING']),
  createdById: z.string()
});

@Injectable()
export class AdminManagementService {
  constructor(
    private prisma: PrismaService,
    private hash: HashService
  ) {}

  async createAdmin(data: unknown) {
    const validated = createAdminSchema.parse(data);
    
    const exists = await this.prisma.admin.findFirst({
      where: {
        OR: [
          { email: validated.email },
          { login: validated.login }
        ]
      }
    });

    if (exists) {
      throw new ConflictException('Admin already exists');
    }

    return this.prisma.$transaction(async (tx) => {
      // Create admin
      const admin = await tx.admin.create({
        data: {
          ...validated,
          password: await this.hash.hashPassword(validated.password),
          history: {
            create: {
              action: 'created',
              userId: validated.createdById
            }
          }
        }
      });

      // Log action
      await tx.actionLog.create({
        data: {
          type: 'admin_created',
          details: {
            adminId: admin.id,
            role: validated.role
          },
          userId: validated.createdById
        }
      });

      return admin;
    });
  }

  async deactivateAdmin(id: string, userId: string, reason: string) {
    return this.prisma.$transaction(async (tx) => {
      // Deactivate admin
      const admin = await tx.admin.update({
        where: { id },
        data: {
          isActive: false,
          history: {
            create: {
              action: 'deactivated',
              userId,
              details: { reason }
            }
          }
        }
      });

      // Log action
      await tx.actionLog.create({
        data: {
          type: 'admin_deactivated',
          details: {
            adminId: id,
            reason
          },
          userId
        }
      });

      return admin;
    });
  }

  async getAdminHistory(id: string) {
    return this.prisma.adminHistory.findMany({
      where: { adminId: id },
      orderBy: { createdAt: 'desc' },
      include: {
        user: {
          select: {
            name: true,
            email: true
          }
        }
      }
    });
  }
}
