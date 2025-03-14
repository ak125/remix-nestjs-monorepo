import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { z } from 'zod';

const pageSchema = z.object({
  title: z.string().min(3),
  content: z.string().min(10),
  language: z.string().length(2).default('fr'),
  userId: z.string(),
  userEmail: z.string().email()
});

@Injectable()
export class PagesService {
  constructor(private prisma: PrismaService) {}

  async getPage(slug: string, language = 'fr') {
    const page = await this.prisma.pageContent.findFirst({
      where: { slug, language }
    });

    if (!page) {
      throw new NotFoundException(`Page ${slug} non trouvée`);
    }

    return page;
  }

  async updatePage(slug: string, data: unknown) {
    const validated = pageSchema.parse(data);

    // Get current version
    const current = await this.getPage(slug, validated.language);

    // Create history record
    await this.prisma.pageHistory.create({
      data: {
        pageId: current.id,
        title: current.title,
        content: current.content,
        version: current.version,
        updatedBy: validated.userId,
        userEmail: validated.userEmail
      }
    });

    // Update page with new version
    return this.prisma.pageContent.update({
      where: { id: current.id },
      data: {
        title: validated.title,
        content: validated.content,
        updatedBy: validated.userId,
        version: { increment: 1 }
      }
    });
  }

  async getPageHistory(slug: string, language = 'fr') {
    const page = await this.getPage(slug, language);

    return this.prisma.pageHistory.findMany({
      where: { pageId: page.id },
      orderBy: { createdAt: 'desc' },
      take: 50
    });
  }
}
