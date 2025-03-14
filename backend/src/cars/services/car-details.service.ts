import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class CarDetailsService {
  constructor(private prisma: PrismaService) {}

  async getCarDetails(params: {
    marqueId: number;
    modeleId: number;
    typeId: number;
  }) {
    const { marqueId, modeleId, typeId } = params;

    const carType = await this.prisma.autoType.findFirst({
      where: {
        TYPE_ID: typeId,
        TYPE_DISPLAY: true,
        AUTO_MODELE: {
          MODELE_ID: modeleId,
          MODELE_DISPLAY: true,
          AUTO_MARQUE: {
            MARQUE_ID: marqueId,
            MARQUE_DISPLAY: true
          }
        }
      },
      include: {
        AUTO_MODELE: {
          include: {
            AUTO_MARQUE: {
              select: {
                MARQUE_ID: true,
                MARQUE_ALIAS: true,
                MARQUE_NAME: true,
                MARQUE_NAME_META: true,
                MARQUE_LOGO: true
              }
            }
          }
        }
      }
    });

    if (!carType) {
      throw new NotFoundException('Car details not found');
    }

    return carType;
  }
}
