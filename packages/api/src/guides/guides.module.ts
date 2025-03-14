import { Module } from "@nestjs/common";
import { GuidesController } from "./guides.controller";
import { GuidesService } from "./guides.service";
import { PrismaService } from "../prisma/prisma.service";

@Module({
  controllers: [GuidesController],
  providers: [GuidesService, PrismaService],
})
export class GuidesModule {}
