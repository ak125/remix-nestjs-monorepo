import { Controller, Get, Param, Query, UseInterceptors, CacheInterceptor } from '@nestjs/common';
import { MarqueService } from '../services/marque.service';

@Controller('marque')
@UseInterceptors(CacheInterceptor)
export class MarqueController {
  constructor(private readonly marqueService: MarqueService) {}

  @Get(':alias')
  async getMarque(@Param('alias') alias: string) {
    return this.marqueService.getMarqueByAlias(alias);
  }

  @Get(':marqueId/models')
  async getModels(
    @Param('marqueId') marqueId: string,
    @Query('page') page = '1',
    @Query('limit') limit = '6'
  ) {
    return this.marqueService.getModelsByMarque(
      Number(marqueId),
      Number(page),
      Number(limit)
    );
  }

  @Get(':marqueId/popular')
  async getPopularModels(@Param('marqueId') marqueId: string) {
    return this.marqueService.getPopularModels(Number(marqueId));
  }
}
