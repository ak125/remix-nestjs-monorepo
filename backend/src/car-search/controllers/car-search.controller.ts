import { Controller, Post, Body, Res, BadRequestException } from '@nestjs/common';
import { Response } from 'express';
import { CarSearchService } from '../services/car-search.service';

@Controller('car-search')
export class CarSearchController {
  constructor(private readonly carSearchService: CarSearchService) {}

  @Post()
  async searchCar(
    @Body('ref_mine') refMine?: string,
    @Body('linkto') linkto?: string,
    @Res() res: Response
  ) {
    if (!refMine || !linkto || linkto !== 'carType') {
      return res.redirect('/welcome');
    }

    const result = await this.carSearchService.findCarByMine(refMine);
    return res.redirect(result.redirectTo);
  }
}
