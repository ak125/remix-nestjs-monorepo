import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class FavoritesService {
  constructor(private prisma: PrismaService) {}

  async addFavorite(userId: string, modelId: number) {
    return this.prisma.userFavorite.create({
      data: {
        userId,
        modelId
      },
      include: {
        model: {
          include: {
            marque: true,
            specifications: true
          }
        }
      }
    });
  }

  async removeFavorite(userId: string, modelId: number) {
    return this.prisma.userFavorite.delete({
      where: {
        userId_modelId: {
          userId,
          modelId
        }
      }
    });
  }

  async getUserFavorites(userId: string) {
    return this.prisma.userFavorite.findMany({
      where: { userId },
      include: {
        model: {
          include: {
            marque: true,
            specifications: true
          }
        }
      },
      orderBy: { createdAt: 'desc' }
    });
  }

  async syncFavorites(userId: string, modelIds: number[]) {
    await this.prisma.userFavorite.deleteMany({
      where: { userId }
    });

    return this.prisma.userFavorite.createMany({
      data: modelIds.map(modelId => ({
        userId,
        modelId
      }))
    });
  }
}
