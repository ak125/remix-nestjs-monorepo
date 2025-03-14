import { Controller, Get, Param, Res, UseInterceptors, CacheInterceptor } from '@nestjs/common';
import { Response } from 'express';
import { MarqueService } from '../services/marque.service';
import { ApiTags, ApiParam } from '@nestjs/swagger';

@ApiTags('Marques')
@Controller('marques')
@UseInterceptors(CacheInterceptor)
export class MarqueController {
  constructor(private readonly marqueService: MarqueService) {}

  @Get(':id')
  @ApiParam({ name: 'id', required: true })
  async getMarque(
    @Param('id') marqueId: string,
    @Res() res: Response
  ) {
    try {
      const data = await this.marqueService.getMarqueWithDetails(Number(marqueId));

      return res.render('marque', {
        marque: data.details,
        seo: data.seo,
        models: data.models,
        meta: data.meta
      });

    } catch (error) {
      if (error.status === 404) {
        return res.status(410).render('410.page');
      }
      throw error;
    }
  }
}
