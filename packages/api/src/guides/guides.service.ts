import { Injectable, NotFoundException } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";

@Injectable()
export class GuidesService {
  constructor(private prisma: PrismaService) {}

  async getGuideByAlias(alias: string) {
    if (!alias) {
      throw new NotFoundException("Alias requis");
    }

    const guide = await this.prisma.blogGuide.findUnique({
      where: { alias },
      select: {
        id: true,
        h1: true,
        alias: true,
        preview: true,
        content: true,
        createdAt: true,
        updatedAt: true,
        seoTitle: true,
        seoDescription: true,
        seoKeywords: true,
        imageUrl: true
      },
    });

    if (!guide) {
      throw new NotFoundException("Guide non trouvé");
    }

    return guide;
  }
}
