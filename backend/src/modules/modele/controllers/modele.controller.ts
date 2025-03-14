import { Controller, Get, Query, UseInterceptors, CacheInterceptor } from '@nestjs/common';
import { ModeleService } from '../services/modele.service';
import { ApiTags, ApiQuery } from '@nestjs/swagger';

@ApiTags('Modeles')
@Controller('modeles')
@UseInterceptors(CacheInterceptor)
export class ModeleController {
  constructor(private readonly modeleService: ModeleService) {}

  @Get()
  @ApiQuery({ name: 'marqueId', required: true })
  @ApiQuery({ name: 'year', required: true })
  async getModeles(
    @Query('marqueId') marqueId: string,
    @Query('year') year: string
  ) {
    const modeles = await this.modeleService.getModelesByMarqueAndYear(
      Number(marqueId),
      Number(year)
    );

    const formattedModeles = await this.modeleService.formatModeleResponse(modeles);

    return {
      options: [
        { value: 0, label: 'Modèle' },
        ...formattedModeles
      ]
    };
  }
}
