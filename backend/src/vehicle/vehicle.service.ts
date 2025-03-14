import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable() 
export class VehicleService {
  constructor(private prisma: PrismaService) {}

  async getVehicleDetails(params: {
    typeId: number;
    marqueId: number;
    modeleId: number;
  }) {
    const { typeId, marqueId, modeleId } = params;

    const vehicle = await this.prisma.autoType.findFirst({
      where: {
        TYPE_ID: typeId,
        TYPE_MARQUE_ID: marqueId,
        TYPE_MODELE_ID: modeleId,
        TYPE_DISPLAY: true
      },
      include: {
        AUTO_MARQUE: true,
        AUTO_MODELE: true,
        AUTO_TYPE_MOTOR_FUEL: true,
        AUTO_TYPE_MOTOR_CODE: true
      }
    });

    if (!vehicle) {
      throw new NotFoundException('Vehicle not found');
    }

    return vehicle;
  }

  async getGammeDetails(pgId: number) {
    const gamme = await this.prisma.piecesGamme.findFirst({
      where: {
        PG_ID: pgId,
        PG_DISPLAY: true
      },
      include: {
        CATALOG_GAMME: {
          include: {
            CATALOG_FAMILY_CATALOG_GAMME_MC_MF_IDToCATALOG_FAMILY: true
          }
        }
      }
    });

    if (!gamme) {
      throw new NotFoundException('Gamme not found');
    }

    return gamme;
  }

  async getCompatiblePieces(params: {
    typeId: number;
    gammeId: number;
  }) {
    const { typeId, gammeId } = params;

    return this.prisma.piece.findMany({
      where: {
        PIECES_RELATION_TYPE: {
          some: {
            RTP_TYPE_ID: typeId,
            RTP_PG_ID: gammeId
          }
        },
        PIECE_DISPLAY: true
      },
      include: {
        marque: true,
        specifications: true,
        prices: {
          where: { PRI_DISPO: true }
        }
      },
      orderBy: {
        PIECE_SORT: 'asc'
      }
    });
  }
}
