import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class MotorisationService {
  constructor(private prisma: PrismaService) {}

  async getMotorisations(params: {
    marqueId: number;
    marqueYear: number;
    modelId: number;
    gammeId: number;
  }) {
    const { marqueId, marqueYear, modelId, gammeId } = params;

    // Validation des paramètres requis
    if (!marqueId || !marqueYear || !modelId || !gammeId) {
      return [{ value: '0', label: '- Motorisation -' }];
    }

    const types = await this.prisma.autoType.findMany({
      where: {
        TYPE_MODELE_ID: modelId,
        TYPE_DISPLAY: true,
        PIECES_RELATION_TYPE: {
          some: {
            RTP_PG_ID: gammeId
          }
        }
      },
      include: {
        PIECES_RELATION_TYPE: {
          include: {
            PIECES_GAMME: true
          }
        }
      },
      orderBy: [
        { TYPE_NAME: 'asc' },
        { TYPE_POWER_PS: 'asc' }
      ]
    });

    return types.map(type => {
      const gamme = type.PIECES_RELATION_TYPE[0]?.PIECES_GAMME;
      const yearTo = type.TYPE_YEAR_TO || marqueYear;

      return {
        value: `${gamme?.PG_ALIAS}-${gammeId}/${type.TYPE_ALIAS}-${type.TYPE_ID}.html`,
        label: `${type.TYPE_NAME} ${type.TYPE_FUEL} ${type.TYPE_POWER_PS} Ch ${type.TYPE_YEAR_FROM}-${yearTo}`
      };
    });
  }
}
