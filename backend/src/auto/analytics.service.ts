import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class AnalyticsService {
  constructor(private prisma: PrismaService) {}

  async getTrendingModels(limit = 5) {
    return this.prisma.modelView.groupBy({
      by: ['modelId'],
      _count: {
        _all: true
      },
      orderBy: {
        _count: {
          _all: 'desc'
        }
      },
      take: limit,
      include: {
        model: true
      }
    });
  }

  async getUserInteractions(userId: string) {
    return this.prisma.userHistory.findMany({
      where: { userId },
      include: {
        model: true
      },
      orderBy: {
        createdAt: 'desc'
      }
    });
  }

  async recordModelView(modelId: number, userId?: string) {
    return this.prisma.modelView.create({
      data: {
        modelId,
        userId
      }
    });
  }
}
