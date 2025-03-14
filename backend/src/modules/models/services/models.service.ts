import { Injectable, Logger, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';

@Injectable()
export class ModelsService {
  private readonly logger = new Logger(ModelsService.name);
  private readonly currentYear = new Date().getFullYear();

  constructor(private readonly prisma: PrismaService) {}

  async getModelsByMarqueAndYear(marqueId: number, year: number) {
    if (!marqueId || !year) {
      return [{ value: 0, label: 'Modèle' }];
    }

    try {
      const models = await this.prisma.autoModele.findMany({
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

      return [
        { value: 0, label: 'Modèle' },
        ...models.map(model => ({
          value: model.id,
          label: `${model.name} ${model.yearFrom}-${model.yearTo || this.currentYear}`
        }))
      ];

    } catch (error) {
      this.logger.error(`Error fetching models: ${error.message}`);
      return [{ value: 0, label: 'Modèle' }];
    }
  }
}
