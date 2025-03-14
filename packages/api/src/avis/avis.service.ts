import { Injectable, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { z } from 'zod';

const avisSchema = z.object({
  nom: z.string().min(2, "Nom trop court"),
  commentaire: z.string().min(10, "Commentaire trop court"),
  note: z.number().min(1).max(5),
  email: z.string().email().optional()
});

@Injectable()
export class AvisService {
  constructor(private prisma: PrismaService) {}

  async getAllAvis(options?: { 
    take?: number;
    skip?: number;
    orderBy?: 'date' | 'note';
    filterByNote?: number;
  }) {
    return this.prisma.avis.findMany({
      take: options?.take || 50,
      skip: options?.skip,
      orderBy: options?.orderBy === 'date' 
        ? { createdAt: 'desc' }
        : options?.orderBy === 'note' 
        ? { note: 'desc' }
        : undefined,
      where: options?.filterByNote
        ? { note: options.filterByNote }
        : undefined
    });
  }

  async addAvis(data: unknown) {
    const validated = avisSchema.parse(data);

    return this.prisma.avis.create({
      data: validated
    });
  }

  async getStats() {
    const avis = await this.prisma.avis.findMany({
      select: { note: true }
    });

    const total = avis.length;
    const sum = avis.reduce((acc, curr) => acc + curr.note, 0);
    const average = total > 0 ? sum / total : 0;

    const distribution = avis.reduce((acc, curr) => {
      acc[curr.note] = (acc[curr.note] || 0) + 1;
      return acc;
    }, {} as Record<number, number>);

    return {
      total,
      average,
      distribution
    };
  }

  async deleteAvis(id: string, adminId: string) {
    const admin = await this.prisma.user.findUnique({
      where: { id: adminId },
      select: { role: true }
    });

    if (admin?.role !== 'ADMIN') {
      throw new BadRequestException('Non autorisé');
    }

    return this.prisma.avis.delete({
      where: { id: Number(id) }
    });
  }
}
