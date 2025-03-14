import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class ConstructeursService {
  constructor(private prisma: PrismaService) {}

  async getMarques(params: {
    top?: boolean;
    search?: string;
  }) {
    const { top, search } = params;

    return this.prisma.autoMarque.findMany({
      where: {
        display: true,
        ...(top && { top: true }),
        ...(search && {
          name: { contains: search, mode: 'insensitive' }
        })
      },
      orderBy: { sort: 'asc' },
      include: {
        _count: {
          select: { models: true }
        }
      }
    });
  }

  async getMarqueWithModels(marqueId: number) {
    const marque = await this.prisma.autoMarque.findFirst({
      where: {
        id: marqueId,
        display: true
      },
      include: {
        models: {
          where: { display: true },
          orderBy: { sort: 'asc' },
          include: {
            types: {
              where: { display: true }
            }
          }
        }
      }
    });

    if (!marque) {
      throw new NotFoundException(`Marque #${marqueId} not found`);
    }

    return marque;
  }

  async getModelsYears(marqueId: number) {
    const models = await this.prisma.autoModel.findMany({
      where: {
        marqueId,
        display: true
      },
      include: {
        types: {
          select: {
            yearFrom: true,
            yearTo: true
          },
          where: { display: true }
        }
      }
    });

    const years = new Set<number>();
    models.forEach(model => {
      model.types.forEach(type => {
        if (type.yearFrom) years.add(type.yearFrom);
        if (type.yearTo) years.add(type.yearTo);
      });
    });

    return Array.from(years).sort((a, b) => b - a);
  }
}
