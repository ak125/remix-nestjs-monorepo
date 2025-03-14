import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';

@Injectable()
export class VehicleService {
  constructor(private prisma: PrismaService) {}

  async getVehicleInfo(marqueId: string, modeleId: string, typeId: string) {
    return await this.prisma.type.findFirst({
      where: {
        id: typeId,
        modeleId,
        modele: { 
          marqueId,
          display: true 
        },
        display: true,
      },
      include: {
        modele: {
          include: {
            marque: true,
          },
        },
        motorCodes: true,
      },
    });
  }

  async getVehicleYears(marqueId: string) {
    const years = await this.prisma.type.findMany({
      where: {
        modele: {
          marqueId,
          display: true,
        },
        display: true,
      },
      select: {
        yearFrom: true,
        yearTo: true,
      },
      distinct: ['yearFrom', 'yearTo'],
      orderBy: {
        yearFrom: 'desc',
      },
    });

    return years.map(y => ({
      value: y.yearFrom,
      label: `${y.yearFrom}${y.yearTo ? ` - ${y.yearTo}` : ''}`,
    }));
  }
}
