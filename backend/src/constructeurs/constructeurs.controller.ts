import { Controller, Get, Param, Query, ParseIntPipe } from '@nestjs/common';
import { ConstructeursService } from './constructeurs.service';

@Controller('constructeurs')
export class ConstructeursController {
  constructor(private readonly constructeursService: ConstructeursService) {}

  @Get()
  getMarques(
    @Query('top') top?: boolean,
    @Query('search') search?: string
  ) {
    return this.constructeursService.getMarques({ top, search });
  }

  @Get(':id')
  getMarque(@Param('id', ParseIntPipe) id: number) {
    return this.constructeursService.getMarqueWithModels(id);
  }

  @Get(':id/years')
  getModelYears(@Param('id', ParseIntPipe) id: number) {
    return this.constructeursService.getModelsYears(id);
  }
}
