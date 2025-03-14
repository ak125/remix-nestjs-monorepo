import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';

@Injectable()
export class CompositService {
  constructor(private readonly prisma: PrismaService) {}

  async validateVehicleAndGamme(typeId: number, pgId: number) {
    const [vehicle, gamme] = await Promise.all([
      this.prisma.autoType.findFirst({
        where: {
          id: typeId,
          display: true,
          modele: { display: true },
          marque: { display: true },
        },
      }),
      this.prisma.piecesGamme.findFirst({
        where: { 
          id: pgId, 
          level: { in: [1, 2] },
          display: true,
        },
      }),
    ]);

    if (!vehicle || !gamme) {
      throw new NotFoundException('Vehicle or gamme not found');
    }

    return { vehicle, gamme };
  }

  async getCompositData(typeId: number, pgId: number) {
    const [partsCount, minPrice] = await Promise.all([
      this.getPartsCount(typeId, pgId),
      this.getMinPrice(typeId, pgId),
    ]);

    if (partsCount === 0) {
      return null;
    }

    const [gammeData, vehicleData] = await Promise.all([
      this.getGammeDetails(pgId),
      this.getVehicleDetails(typeId),
    ]);

    return {
      partsCount,
      minPrice: minPrice?._min?.venteTTC || 0,
      gamme: gammeData,
      vehicle: vehicleData,
    };
  }

  private async getPartsCount(typeId: number, pgId: number) {
    return this.prisma.piecesRelationType.count({
      where: {
        typeId,
        pgId,
        piece: { display: true },
      },
    });
  }

  private async getMinPrice(typeId: number, pgId: number) {
    return this.prisma.piecesPrice.aggregate({
      where: {
        piece: {
          relations: {
            some: { typeId, pgId },
          },
          display: true,
        },
        dispo: true,
      },
      _min: { venteTTC: true },
    });
  }

  private async getGammeDetails(pgId: number) {
    return this.prisma.piecesGamme.findUnique({
      where: { id: pgId },
      select: {
        alias: true,
        name: true,
      },
    });
  }

  private async getVehicleDetails(typeId: number) {
    return this.prisma.autoType.findUnique({
      where: { id: typeId },
      select: {
        alias: true,
        name: true,
        powerPs: true,
        fuel: true,
      },
    });
  }
}
