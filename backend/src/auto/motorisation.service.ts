import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class MotorisationService {
  constructor(private prisma: PrismaService) {}

  async getMotorisations(params: {
    marqueId: number;
    modeleId: number;
    year: number;
  }) {
    const { marqueId, modeleId, year } = params;
    const currentYear = new Date().getFullYear();

    const types = await this.prisma.autoType.findMany({
      where: {
        marqueId,
        modeleId,
        yearFrom: { lte: year },
        yearTo: {
          gte: year,
          equals: null
        },
        isDisplayed: true
      },
      include: {
        specifications: true
      },
      orderBy: [
        { fuel: 'asc' },
        { powerPS: 'desc' }
      ]
    });

    return this.groupByFuelType(types);
  }

  private groupByFuelType(types: any[]) {
    const grouped = types.reduce((acc, type) => {
      const key = type.fuel;
      if (!acc[key]) {
        acc[key] = [];
      }
      acc[key].push({
        id: type.id,
        name: type.name,
        alias: type.alias,
        power: `${type.powerPS} Ch`,
        years: `${type.yearFrom}-${type.yearTo || 'Actuel'}`,
        specifications: type.specifications
      });
      return acc;
    }, {});

    return Object.entries(grouped).map(([fuel, motors]) => ({
      fuel,
      motors
    }));
  }
}
