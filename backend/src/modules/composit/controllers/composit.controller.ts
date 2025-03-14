import { Controller, Get, Query, Res, HttpStatus } from '@nestjs/common';
import { Response } from 'express';
import { CompositService } from '../services/composit.service';
import { ApiTags, ApiQuery } from '@nestjs/swagger';

@ApiTags('Composit')
@Controller('composit')
export class CompositController {
  constructor(private readonly compositService: CompositService) {}

  @Get()
  @ApiQuery({ name: 'pg_id', required: true, type: 'number' })
  @ApiQuery({ name: 'marque_id', required: true, type: 'number' })
  @ApiQuery({ name: 'modele_id', required: true, type: 'number' })
  @ApiQuery({ name: 'type_id', required: true, type: 'number' })
  async getComposit(
    @Query('pg_id') pgId: number,
    @Query('type_id') typeId: number,
    @Res() res: Response
  ) {
    try {
      await this.compositService.validateVehicleAndGamme(typeId, pgId);
      
      const data = await this.compositService.getCompositData(typeId, pgId);
      if (!data) {
        return res.status(HttpStatus.PRECONDITION_FAILED).render('412.page');
      }

      return res.status(HttpStatus.OK).render('composit', {
        gammeData: data.gamme,
        carData: data.vehicle,
        articleCount: data.partsCount,
        minPrice: data.minPrice,
      });

    } catch (error) {
      if (error.status === HttpStatus.NOT_FOUND) {
        return res.status(HttpStatus.GONE).render('410.page');
      }
      throw error;
    }
  }
}
