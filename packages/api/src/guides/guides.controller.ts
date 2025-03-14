import { Controller, Get, Query, NotFoundException } from "@nestjs/common";
import { GuidesService } from "./guides.service";

@Controller("guides")
export class GuidesController {
  constructor(private readonly guidesService: GuidesService) {}

  @Get()
  async getGuide(@Query("alias") alias: string) {
    if (!alias) {
      throw new NotFoundException("Alias requis");
    }
    return await this.guidesService.getGuideByAlias(alias);
  }
}
