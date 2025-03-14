import { Controller, Get, Param, Req } from "@nestjs/common";
import { MarqueService } from "./marque.service";
import { Request } from "express";

@Controller("marque")
export class MarqueController {
  constructor(private readonly marqueService: MarqueService) {}

  @Get(":marque_alias")
  async getMarque(@Req() req: Request, @Param() params: any) {
    return this.marqueService.getMarque(req, params);
  }
}
