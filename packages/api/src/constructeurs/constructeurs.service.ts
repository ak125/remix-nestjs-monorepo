import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";

@Injectable()
export class ConstructeursService {
  constructor(private prisma: PrismaService) {}

  async getAllConstructeurs() {
    return this.prisma.marque.findMany({
      where: { display: true },
      select: {
        id: true,
        alias: true,
        name: true, 
        metaName: true,
        logoUrl: true,
      },
      orderBy: { sort: "asc" },
    });
  }

  async getConstructeurByAlias(alias: string) {
    const constructeur = await this.prisma.marque.findUnique({
      where: { alias },
      select: {
        id: true,
        alias: true, 
        name: true,
        metaName: true,
        logoUrl: true,
      },
    });

    if (!constructeur) {
      throw new Error("Constructeur non trouvé");
    }

    return constructeur;
  }
}
