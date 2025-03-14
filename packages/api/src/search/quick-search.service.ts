import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { z } from 'zod';

const searchQuerySchema = z.object({
  query: z.string().min(2),
  limit: z.number().min(1).max(10).default(5)
});

@Injectable()
export class QuickSearchService {
  private readonly logger = new Logger(QuickSearchService.name);

  constructor(private prisma: PrismaService) {}

  async search(data: unknown) {
    const { query, limit } = searchQuerySchema.parse(data);

    try {
      const results = await this.prisma.product.findMany({
        where: {
          AND: [
            {
              OR: [
                { name: { contains: query, mode: 'insensitive' } },
                { reference: { contains: query, mode: 'insensitive' } }
              ]
            },
            { isActive: true },
            { level: { in: [1, 2] } }
          ]
        },
        select: {
          id: true, 
          name: true,
          alias: true,
          reference: true,
          category: {
            select: {
              name: true,
              alias: true
            }
          }
        },
        orderBy: { searchCount: 'desc' },
        take: limit
      });

      // Increment search counts
      await this.prisma.$transaction(
        results.map(result => 
          this.prisma.product.update({
            where: { id: result.id },
            data: { searchCount: { increment: 1 } }
          })
        )
      );

      return results;
    } catch (error) {
      this.logger.error(`Search error: ${error.message}`, error.stack);
      return [];
    }
  }
}
