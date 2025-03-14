import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { z } from "zod";

const pageSchema = z.object({
  title: z.string(),
  description: z.string(),
  keywords: z.string(),
  content: z.string()
});

@Injectable()
export class PsService {
  constructor(private prisma: PrismaService) {}

  async getPageData() {
    // Simuler les données de la base pour l'instant
    const data = {
      title: "Page PS",
      description: "Bienvenue sur notre page PS",
      keywords: "page, ps, informations",
      content: "Voici le contenu de la page PS..."
    };

    return pageSchema.parse(data);
  }
}
