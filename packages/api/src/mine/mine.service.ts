import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { z } from 'zod';

const searchSchema = z.object({
  query: z.string().min(2),
  type: z.string().optional(),
  location: z.string().optional(),
  active: z.boolean().default(true)
});

@Injectable()
export class MineService {
  constructor(private prisma: PrismaService) {}

  async searchMines(data: unknown) {
    const { query, type, location, active } = searchSchema.parse(data);

    return this.prisma.mine.findMany({
      where: {
        AND: [
          {
            OR: [
              { name: { contains: query, mode: 'insensitive' } },
              { type: { contains: query, mode: 'insensitive' } }
            ]
          },
          type ? { type: { equals: type } } : {},
          location ? { location: { equals: location } } : {},
          { active }
        ]
      },
      orderBy: {
        name: 'asc'
      }
    });
  }

  async getTypes() {
    const types = await this.prisma.mine.groupBy({
      by: ['type'],
      _count: true
    });

    return types.map(t => ({
      name: t.type,
      count: t._count
    }));
  }

  async getLocations() {
    const locations = await this.prisma.mine.groupBy({
      by: ['location'],
      _count: true
    });

    return locations.map(l => ({
      name: l.location,
      count: l._count
    }));
  }
}
