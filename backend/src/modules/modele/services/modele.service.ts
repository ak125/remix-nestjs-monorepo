import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';

@Injectable()
export class ModeleService {
  private readonly logger = new Logger(ModeleService.name);
  private readonly currentYear = new Date().getFullYear();

  constructor(private readonly prisma: PrismaService) {}

  async getModelesByMarqueAndYear(marqueId: number, year: number) {
    if (!marqueId || !year) {
      return [];
    }

    return this.prisma.modele.findMany({
      where: {
        marqueId,
        display: true,
        yearFrom: {
          lte: year
        },
        OR: [
          { yearTo: null },
          { yearTo: { gte: year } }
        ]
      },
      select: {
        id: true,
        name: true,
        yearFrom: true,
        yearTo: true,
        sort: true
      },
      orderBy: {
        sort: 'asc'
      }
    });
  }

  async formatModeleResponse(modeles: any[]) {
    return modeles.map(modele => ({
      value: modele.id,
      label: `${modele.name} ${modele.yearFrom}-${modele.yearTo || this.currentYear}`
    }));
  }
}
