import { Controller, Get, Query, UseInterceptors, CacheInterceptor } from '@nestjs/common';
import { ModelsService } from '../services/models.service';
import { ApiTags, ApiQuery } from '@nestjs/swagger';

@ApiTags('Models')
@Controller('models')
@UseInterceptors(CacheInterceptor)
export class ModelsController {
  constructor(private readonly modelsService: ModelsService) {}

  @Get()
  @ApiQuery({ name: 'marqueId', required: true })
  @ApiQuery({ name: 'year', required: true })
  async getModels(
    @Query('marqueId') marqueId: string,
    @Query('year') year: string
  ) {
    const options = await this.modelsService.getModelsByMarqueAndYear(
      Number(marqueId), 
      Number(year)
    );

    return { options };
  }
}
