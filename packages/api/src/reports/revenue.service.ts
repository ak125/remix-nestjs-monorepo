import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import * as Stripe from "stripe";
import * as ExcelJS from "exceljs";
import { z } from "zod";

const dateRangeSchema = z.object({
  startDate: z.string(),
  endDate: z.string(),
  period: z.enum(['day', 'week', 'month', 'year'])
});

@Injectable()
export class RevenueService {
  private stripe: Stripe;

  constructor(private prisma: PrismaService) {
    this.stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
      apiVersion: "2023-10-16"
    });
  }

  async getRevenue(data: unknown) {
    const { startDate, endDate, period } = dateRangeSchema.parse(data);
    
    const [payments, subscriptions] = await Promise.all([
      this.stripe.charges.list({
        created: {
          gte: new Date(startDate).getTime() / 1000,
          lte: new Date(endDate).getTime() / 1000
        },
        limit: 100
      }),
      this.stripe.subscriptions.list({
        created: {
          gte: new Date(startDate).getTime() / 1000,
          lte: new Date(endDate).getTime() / 1000
        },
        limit: 100
      })
    ]);

    const revenueByPeriod = this.groupByPeriod(
      [...payments.data, ...subscriptions.data],
      period
    );

    return {
      summary: this.calculateSummary(revenueByPeriod),
      details: revenueByPeriod
    };
  }

  async generateExcelReport(data: unknown) {
    const { details } = await this.getRevenue(data);
    
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Revenus');

    worksheet.columns = [
      { header: 'Période', key: 'period', width: 15 },
      { header: 'Paiements', key: 'payments', width: 15 },
      { header: 'Abonnements', key: 'subscriptions', width: 15 },
      { header: 'Total', key: 'total', width: 15 }
    ];

    details.forEach(d => {
      worksheet.addRow({
        period: d.period,
        payments: d.payments / 100,
        subscriptions: d.subscriptions / 100,
        total: (d.payments + d.subscriptions) / 100
      });
    });

    const buffer = await workbook.xlsx.writeBuffer();
    return buffer;
  }

  private groupByPeriod(transactions: any[], period: string) {
    const groups = new Map();

    transactions.forEach(t => {
      const date = new Date(t.created * 1000);
      const key = this.getPeriodKey(date, period);
      
      const current = groups.get(key) || {
        period: key,
        payments: 0,
        subscriptions: 0
      };

      if ('amount' in t) {
        current.payments += t.amount;
      } else {
        current.subscriptions += t.plan?.amount || 0;
      }

      groups.set(key, current);
    });

    return Array.from(groups.values());
  }

  private getPeriodKey(date: Date, period: string): string {
    switch (period) {
      case 'day':
        return date.toISOString().split('T')[0];
      case 'week':
        return `Semaine ${this.getWeekNumber(date)}`;
      case 'month':
        return date.toLocaleString('fr-FR', { month: 'long', year: 'numeric' });
      case 'year':
        return date.getFullYear().toString();
      default:
        return date.toISOString().split('T')[0];
    }
  }

  private getWeekNumber(date: Date): number {
    const d = new Date(date);
    d.setHours(0, 0, 0, 0);
    d.setDate(d.getDate() + 4 - (d.getDay() || 7));
    const yearStart = new Date(d.getFullYear(), 0, 1);
    return Math.ceil(((d.getTime() - yearStart.getTime()) / 86400000 + 1) / 7);
  }

  private calculateSummary(data: any[]) {
    return data.reduce((acc, d) => ({
      totalPayments: acc.totalPayments + d.payments,
      totalSubscriptions: acc.totalSubscriptions + d.subscriptions
    }), { totalPayments: 0, totalSubscriptions: 0 });
  }
}
