import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { z } from "zod";

const recSchema = z.object({
  title: z.string(),
  description: z.string(),
  content: z.string(),
  lastUpdated: z.string().datetime().optional()
});

@Injectable()
export class RecommendationsService {
  constructor(private prisma: PrismaService) {}

  async getRecommendations() {
    const page = await this.prisma.page.findUnique({
      where: { slug: "recommendations" }
    });

    if (!page) {
      throw new Error("Recommandations non trouvées");
    }

    return recSchema.parse({
      title: page.title,
      description: page.description,
      content: page.content,
      lastUpdated: page.updatedAt
    });
  }
}
