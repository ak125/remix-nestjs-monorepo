import { Injectable, ConflictException, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { HashService } from '../hash/hash.service';
import * as Stripe from "stripe";
import { z } from "zod";

const dateRangeSchema = z.object({
  startDate: z.string(),
  endDate: z.string()
});

const createAdminSchema = z.object({
  email: z.string().email(),
  name: z.string().min(2),
  firstName: z.string().min(2),
  password: z.string().min(8),
  phone: z.string().optional(),
  role: z.enum(['ADMIN', 'COMMERCIAL', 'SHIPPING'])
});

@Injectable()
export class AdminService {
  private stripe: Stripe;

  constructor(
    private prisma: PrismaService,
    private hash: HashService
  ) {
    this.stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
      apiVersion: "2023-10-16"
    });
  }

  async getStats(startDate: string, endDate: string) {
    const { startDate: start, endDate: end } = dateRangeSchema.parse({
      startDate,
      endDate
    });

    const startTimestamp = new Date(start).getTime() / 1000;
    const endTimestamp = new Date(end).getTime() / 1000;

    const [charges, refunds] = await Promise.all([
      this.stripe.charges.list({
        created: { gte: startTimestamp, lte: endTimestamp }
      }),
      this.stripe.refunds.list({
        created: { gte: startTimestamp, lte: endTimestamp }
      })
    ]);

    const total = charges.data.reduce((sum, charge) => sum + charge.amount, 0);
    const pending = charges.data
      .filter(c => c.status === "pending")
      .reduce((sum, charge) => sum + charge.amount, 0);
    const refunded = refunds.data
      .reduce((sum, refund) => sum + refund.amount, 0);

    return {
      stats: {
        total: total / 100,
        pending: pending / 100,
        refunded: refunded / 100
      },
      monthlyData: this.getMonthlyData(charges.data, refunds.data)
    };
  }

  private getMonthlyData(charges: Stripe.Charge[], refunds: Stripe.Refund[]) {
    const monthlyStats = new Map<string, { total: number; refunds: number }>();

    charges.forEach(charge => {
      const month = new Date(charge.created * 1000).toISOString().slice(0, 7);
      const current = monthlyStats.get(month) || { total: 0, refunds: 0 };
      monthlyStats.set(month, {
        ...current,
        total: current.total + charge.amount / 100
      });
    });

    refunds.forEach(refund => {
      const month = new Date(refund.created * 1000).toISOString().slice(0, 7);
      const current = monthlyStats.get(month) || { total: 0, refunds: 0 };
      monthlyStats.set(month, {
        ...current,
        refunds: current.refunds + refund.amount / 100
      });
    });

    return Array.from(monthlyStats.entries())
      .map(([month, data]) => ({
        month,
        ...data
      }))
      .sort((a, b) => a.month.localeCompare(b.month));
  }

  async createAdmin(data: unknown) {
    const validated = createAdminSchema.parse(data);
    
    const existing = await this.prisma.admin.findUnique({
      where: { email: validated.email }
    });

    if (existing) {
      throw new ConflictException('Email already exists');
    }

    const hashedPassword = await this.hash.hashPassword(validated.password);

    return this.prisma.admin.create({
      data: {
        ...validated,
        password: hashedPassword
      }
    });
  }

  async deactivateAdmin(id: string, actorId: string) {
    const admin = await this.getAdmin(id);

    // Prevent self-deactivation
    if (id === actorId) {
      throw new ConflictException('Cannot deactivate yourself');
    }

    return this.prisma.$transaction(async (tx) => {
      // Deactivate admin
      const updated = await tx.admin.update({
        where: { id },
        data: { isActive: false }
      });

      // Log action
      await tx.adminHistory.create({
        data: {
          adminId: actorId,
          action: 'deactivate_admin',
          details: {
            targetAdmin: admin.email
          }
        }
      });

      // Remove session if exists
      await tx.adminSession.deleteMany({
        where: { adminId: id }
      });

      return updated;
    });
  }

  private async getAdmin(id: string) {
    const admin = await this.prisma.admin.findUnique({
      where: { id }
    });

    if (!admin) {
      throw new NotFoundException(`Admin ${id} not found`);
    }

    return admin;
  }
}
