import { prisma } from "~/lib/db.server";
import type { PeriodFilter, DeviceType } from "~/types/analytics";

interface AnalyticsData {
  visits: number;
  conversions: number;
  revenue: number;
}

interface ComparisonResult {
  current: AnalyticsData;
  previous: AnalyticsData;
  trends: {
    visits: number;
    conversions: number;
    revenue: number;
  };
  byDevice: Record<DeviceType, AnalyticsData>;
}

export class AnalyticsService {
  async getComparativeAnalytics(
    period: PeriodFilter,
    endDate: Date = new Date()
  ): Promise<ComparisonResult> {
    const { startDate, previousStartDate } = this.calculatePeriodDates(period, endDate);

    const [currentData, previousData, deviceData] = await Promise.all([
      this.getPeriodData(startDate, endDate),
      this.getPeriodData(previousStartDate, startDate),
      this.getDeviceBreakdown(startDate, endDate)
    ]);

    return {
      current: currentData,
      previous: previousData,
      trends: {
        visits: this.calculateTrend(currentData.visits, previousData.visits),
        conversions: this.calculateTrend(currentData.conversions, previousData.conversions),
        revenue: this.calculateTrend(currentData.revenue, previousData.revenue)
      },
      byDevice: deviceData
    };
  }

  private calculatePeriodDates(period: PeriodFilter, endDate: Date) {
    const periodDays = {
      '7d': 7,
      '30d': 30,
      '90d': 90,
      '1y': 365
    }[period];

    const startDate = new Date(endDate);
    startDate.setDate(startDate.getDate() - periodDays);

    const previousStartDate = new Date(startDate);
    previousStartDate.setDate(previousStartDate.getDate() - periodDays);

    return { startDate, previousStartDate };
  }

  private async getPeriodData(startDate: Date, endDate: Date): Promise<AnalyticsData> {
    const data = await prisma.analytics.aggregate({
      where: {
        createdAt: {
          gte: startDate,
          lt: endDate
        }
      },
      _sum: {
        visits: true,
        conversions: true,
        revenue: true
      }
    });

    return {
      visits: data._sum.visits || 0,
      conversions: data._sum.conversions || 0,
      revenue: data._sum.revenue || 0
    };
  }

  private async getDeviceBreakdown(
    startDate: Date, 
    endDate: Date
  ): Promise<Record<DeviceType, AnalyticsData>> {
    const data = await prisma.analytics.groupBy({
      by: ['deviceType'],
      where: {
        createdAt: {
          gte: startDate,
          lt: endDate
        }
      },
      _sum: {
        visits: true,
        conversions: true,
        revenue: true
      }
    });

    return data.reduce((acc, curr) => ({
      ...acc,
      [curr.deviceType]: {
        visits: curr._sum.visits || 0,
        conversions: curr._sum.conversions || 0,
        revenue: curr._sum.revenue || 0
      }
    }), {} as Record<DeviceType, AnalyticsData>);
  }

  private calculateTrend(current: number, previous: number): number {
    if (previous === 0) return 0;
    return ((current - previous) / previous) * 100;
  }
}
