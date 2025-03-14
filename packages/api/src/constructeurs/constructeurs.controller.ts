import { Controller, Get, Query } from "@nestjs/common";
import { ConstructeursService } from "./constructeurs.service";

@Controller("constructeurs")
export class ConstructeursController {
  constructor(private readonly constructeursService: ConstructeursService) {}

  @Get()
  async getAll() {
    return await this.constructeursService.getAllConstructeurs();
  }

  @Get("details")
  async getByAlias(@Query("alias") alias: string) {
    return await this.constructeursService.getConstructeurByAlias(alias);
  }
}
