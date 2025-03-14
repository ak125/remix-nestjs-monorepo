import { Injectable, NotFoundException } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { Request } from "express";
import { z } from "zod";

@Injectable()
export class MarqueService {
  constructor(private prisma: PrismaService) {}

  private marqueSchema = z.object({
    marque_alias: z.string().min(2).max(50)
  });

  async getMarque(req: Request, params: any) {
    const parsed = this.marqueSchema.safeParse(params);
    if (!parsed.success) {
      throw new NotFoundException("Paramètres invalides");
    }

    const { marque_alias } = parsed.data;

    const marque = await this.prisma.marque.findUnique({
      where: { alias: marque_alias }
    });

    if (!marque || !marque.display) {
      throw new NotFoundException("Marque introuvable");
    }

    return {
      id: marque.id,
      name: marque.name,
      meta: marque.metaName,
      alias: marque.alias,
      logo: `/upload/constructeurs-automobiles/marques-logos/${marque.logo}`
    };
  }
}
