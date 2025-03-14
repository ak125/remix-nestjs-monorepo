import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';

@Injectable()
export class PartsService {
  constructor(private prisma: PrismaService) {}

  async getCompatibleParts(typeId: string, gammeId: string) {
    return await this.prisma.piece.findMany({
      where: {
        display: true,
        relations: {
          some: {
            typeId,
            pgId: gammeId,
          },
        },
      },
      include: {
        manufacturer: true,
        prices: {
          where: {
            isActive: true,
          },
        },
        medias: {
          where: {
            display: true,
          },
          take: 1,
        },
        technicalSpecs: {
          include: {
            criterion: true,
          },
        },
      },
    });
  }

  async getAvailableFilters(typeId: string, gammeId: string) {
    const [qualities, manufacturers, sideFilters] = await Promise.all([
      this.getQualityFilters(typeId, gammeId),
      this.getManufacturerFilters(typeId, gammeId),
      this.getSideFilters(typeId, gammeId),
    ]);

    return {
      qualities,
      manufacturers,
      sideFilters,
    };
  }

  private async getQualityFilters(typeId: string, gammeId: string) {
    return this.prisma.pieceQuality.findMany({
      where: {
        pieces: {
          some: {
            relations: {
              some: {
                typeId,
                pgId: gammeId,
              },
            },
          },
        },
      },
      select: {
        id: true,
        name: true,
        _count: {
          select: {
            pieces: true,
          },
        },
      },
    });
  }

  // ... autres méthodes de filtres
}
