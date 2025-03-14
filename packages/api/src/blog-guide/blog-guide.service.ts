import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";

@Injectable()
export class BlogGuideService {
  constructor(private prisma: PrismaService) {}

  async getRecentArticles() {
    return await this.prisma.blogGuide.findMany({
      orderBy: [
        { updatedAt: "desc" }, 
        { createdAt: "desc" }
      ],
      select: {
        id: true,
        title: true,
        alias: true,
        preview: true,
        image: true,
        createdAt: true,
        updatedAt: true,
      }
    });
  }

  async getMostReadArticles() {
    return await this.prisma.blogGuide.findMany({
      orderBy: [
        { visitCount: "desc" },
        { updatedAt: "desc" }
      ],
      take: 6,
      select: {
        id: true,
        title: true,
        alias: true,
        preview: true,
        image: true,
        createdAt: true,
        updatedAt: true
      }
    });
  }
}
