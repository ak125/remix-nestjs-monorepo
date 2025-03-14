import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class PieceService {
  constructor(private prisma: PrismaService) {}

  async findPieces(params: {
    typeId: number;
    marqueId: number;
    modeleId: number;
    gammeId: number;
    equipementId?: number;
    essieuId?: number;
  }) {
    return this.prisma.piece.findMany({
      where: {
        typeId: params.typeId,
        marqueId: params.marqueId,
        modeleId: params.modeleId,
        gammeId: params.gammeId,
        equipementId: params.equipementId,
        essieuId: params.essieuId,
        display: true,
      },
      include: {
        equipement: true,
        specifications: true,
        prices: {
          where: { isActive: true }
        }
      },
      orderBy: [
        { equipementId: 'asc' },
        { prices: { price: 'asc' } }
      ]
    });
  }

  async getFilters(typeId: number) {
    const [equipements, essieux] = await Promise.all([
      this.prisma.equipement.findMany({
        where: { 
          pieces: { some: { typeId } },
          isActive: true 
        },
        orderBy: { name: 'asc' }
      }),
      this.prisma.essieu.findMany({
        where: {
          pieces: { some: { typeId } },
          isActive: true
        },
        orderBy: { name: 'asc' }
      })
    ]);

    return { equipements, essieux };
  }
}
