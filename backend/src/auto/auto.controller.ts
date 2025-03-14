import { Controller, Get, Query, NotFoundException } from '@nestjs/common';
import { AutoService } from './auto.service';

@Controller('auto')
export class AutoController {
  constructor(private readonly autoService: AutoService) {}

  @Get('details') 
  async getAutoDetails(
    @Query('marque_id') marque_id: string,
    @Query('modele_id') modele_id: string,
    @Query('type_id') type_id: string
  ) {
    const autoData = await this.autoService.getAutoDetails(
      Number(marque_id),
      Number(modele_id), 
      Number(type_id)
    );

    if (!autoData) {
      throw new NotFoundException('Aucune donnée trouvée pour ces paramètres.');
    }

    return autoData;
  }
}
