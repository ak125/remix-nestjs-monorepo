import { Injectable, Logger, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';

@Injectable()
export class MineService {
  private readonly logger = new Logger(MineService.name);

  constructor(private readonly prisma: PrismaService) {}

  async findCarByMineCode(mineCode: string) {
    const vehicle = await this.prisma.autoTypeNumberCode.findFirst({
      where: {
        cnit: mineCode,
        type: {
          display: true,
          modele: {
            display: true,
            marque: {
              display: true
            }
          }
        }
      },
      include: {
        type: {
          include: {
            modele: {
              include: {
                marque: true
              }
            }
          }
        }
      }
    });

    if (!vehicle) {
      throw new NotFoundException('Véhicule non trouvé');
    }

    return {
      url: this.generateCarUrl(vehicle),
      vehicle: vehicle.type
    };
  }

  async findCarWithParts(mineCode: string, pgId: number) {
    const vehicle = await this.prisma.autoTypeNumberCode.findFirst({
      where: {
        cnit: mineCode,
        type: {
          display: true,
          modele: {
            display: true
          },
          relations: {
            some: {
              pgId,
              piece: {
                display: true
              }
            }
          }
        }
      },
      include: {
        type: {
          include: {
            modele: {
              include: {
                marque: true
              }
            }
          }
        }
      }
    });

    if (!vehicle) {
      throw new NotFoundException('Aucune pièce trouvée pour ce véhicule');
    }

    return {
      url: this.generatePartUrl(vehicle, pgId),
      vehicle: vehicle.type
    };
  }

  private generateCarUrl(vehicle: any) {
    const { type, modele, marque } = vehicle.type;
    return `/auto/${marque.alias}-${marque.id}/${modele.alias}-${modele.id}/${type.alias}-${type.id}.html`;
  }

  private generatePartUrl(vehicle: any, pgId: number) {
    const { type, modele, marque } = vehicle.type;
    return `/pieces/${pgId}/${marque.alias}-${marque.id}/${modele.alias}-${modele.id}/${type.alias}-${type.id}.html`;
  }
}
