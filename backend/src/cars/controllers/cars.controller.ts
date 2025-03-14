import { Controller, Get, Query, ParseIntPipe, NotFoundException } from '@nestjs/common';
import { CarsService } from '../services/cars.service';

@Controller('cars')
export class CarsController {
  constructor(private readonly carsService: CarsService) {}

  @Get('details')
  async getCarDetails(
    @Query('pg_id', ParseIntPipe) pg_id: number,
    @Query('marque_id', ParseIntPipe) marque_id: number,
    @Query('modele_id', ParseIntPipe) modele_id: number,
    @Query('type_id', ParseIntPipe) type_id: number,
    @Query('filtre_union', ParseIntPipe) filtre_piece_fil_id?: number,
    @Query('filtre_essieu', ParseIntPipe) filtre_psf_id?: number,
    @Query('filtre_equip', ParseIntPipe) filtre_pm_id?: number
  ) {
    const result = await this.carsService.getCarDetails({
      pg_id,
      marque_id,
      modele_id, 
      type_id,
      filtre_piece_fil_id: filtre_piece_fil_id || 0,
      filtre_psf_id: filtre_psf_id || 0,
      filtre_pm_id: filtre_pm_id || 0
    });

    if (!result) {
      throw new NotFoundException('Données introuvables');
    }

    return result;
  }

  @Get('years')
  async getYears(
    @Query('marqueId', ParseIntPipe) marqueId: number
  ) {
    return this.carsService.getYears({ marqueId });
  }
}
