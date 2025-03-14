import { Controller, Get, Query, NotFoundException } from '@nestjs/common';
import { MarqueService } from './marque.service';

@Controller('marque')
export class MarqueController {
  constructor(private readonly marqueService: MarqueService) {}

  @Get('details')
  async getMarqueDetails(@Query('marque_id') marqueId: string) {
    const marqueData = await this.marqueService.getMarqueDetails(Number(marqueId));

    if (!marqueData) {
      throw new NotFoundException('Aucune donnée trouvée pour cette marque.');
    }

    return marqueData;
  }
}
