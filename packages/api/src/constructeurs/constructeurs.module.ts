import { Module } from "@nestjs/common";
import { ConstructeursController } from "./constructeurs.controller";
import { ConstructeursService } from "./constructeurs.service";
import { PrismaService } from "../prisma/prisma.service";

@Module({
  controllers: [ConstructeursController],
  providers: [ConstructeursService, PrismaService],
})
export class ConstructeursModule {}
