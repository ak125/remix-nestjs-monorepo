import { Module } from "@nestjs/common";
import { BlogGuideController } from "./blog-guide.controller";
import { BlogGuideService } from "./blog-guide.service";
import { PrismaService } from "../prisma/prisma.service";

@Module({
  controllers: [BlogGuideController],
  providers: [BlogGuideService, PrismaService],
  exports: [BlogGuideService]
})
export class BlogGuideModule {}
