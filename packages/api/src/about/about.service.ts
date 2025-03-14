import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { z } from "zod";

const aboutSchema = z.object({
  title: z.string(),
  description: z.string(),
  keywords: z.string(),
  content: z.string()
});

@Injectable()
export class AboutService {
  constructor(private prisma: PrismaService) {}

  async getPageData() {
    const rawData = {
      title: "À propos de nous",
      description: "Découvrez qui nous sommes et notre mission",
      keywords: "about us, entreprise, mission",
      content: "Contenu de la page à propos..."
    };

    return aboutSchema.parse(rawData);
  }
}
