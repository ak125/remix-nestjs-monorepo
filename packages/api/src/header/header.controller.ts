import { Controller, Get, Req } from "@nestjs/common";
import { HeaderService } from "./header.service";
import { Request } from "express";

@Controller("header")
export class HeaderController {
  constructor(private readonly headerService: HeaderService) {}

  @Get()
  async getHeaderData(@Req() req: Request) {
    return this.headerService.getHeaderData(req);
  }
}
