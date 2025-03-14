import { Controller, Get, Query, ParseIntPipe } from '@nestjs/common';
import { GammeService } from '../services/gamme.service';

@Controller('gamme')
export class GammeController {
  constructor(private readonly gammeService: GammeService) {}

  @Get('details')
  async getGammeDetails(
    @Query('pg_id', ParseIntPipe) pg_id: number,
    @Query('marque_id', ParseIntPipe) marque_id: number,
    @Query('modele_id', ParseIntPipe) modele_id: number,
    @Query('type_id', ParseIntPipe) type_id: number
  ) {
    return this.gammeService.getGammeDetails({
      pg_id,
      marque_id,
      modele_id,
      type_id
    });
  }
}
