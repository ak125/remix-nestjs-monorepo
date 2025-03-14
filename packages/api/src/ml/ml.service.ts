import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { z } from 'zod';

const mlSchema = z.object({
  title: z.string().min(3),
  description: z.string().min(10),
  content: z.string().min(50),
  category: z.string().optional(),
  tags: z.array(z.string()).default([]),
  imageUrl: z.string().url().optional()
});

@Injectable()
export class MLService {
  constructor(private prisma: PrismaService) {}

  async getMLContent(options?: {
    category?: string;
    tags?: string[];
    searchTerm?: string;
  }) {
    return this.prisma.mLContent.findMany({
      where: {
        AND: [
          { published: true },
          options?.category ? { category: options.category } : {},
          options?.tags?.length ? { tags: { hasEvery: options.tags } } : {},
          options?.searchTerm ? {
            OR: [
              { title: { contains: options.searchTerm, mode: 'insensitive' } },
              { description: { contains: options.searchTerm, mode: 'insensitive' } }
            ]
          } : {}
        ]
      },
      orderBy: { createdAt: 'desc' }
    });
  }

  async getMLCategories() {
    const contents = await this.prisma.mLContent.groupBy({
      by: ['category'],
      _count: true,
      where: { published: true }
    });

    return contents
      .filter(c => c.category)
      .map(c => ({
        name: c.category,
        count: c._count
      }));
  }

  async incrementViewCount(id: string) {
    return this.prisma.mLContent.update({
      where: { id },
      data: { viewCount: { increment: 1 } }
    });
  }

  async createMLContent(data: unknown) {
    const validated = mlSchema.parse(data);

    return this.prisma.mLContent.create({
      data: validated
    });
  }
}
