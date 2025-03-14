import { Controller, Post, Body, Res, BadRequestException, UseInterceptors, CacheInterceptor } from '@nestjs/common';
import { Response } from 'express';
import { MineService } from '../services/mine.service';
import { ApiTags, ApiBody } from '@nestjs/swagger';

@ApiTags('Type Mine')
@Controller('mine')
@UseInterceptors(CacheInterceptor)
export class MineController {
  constructor(private readonly mineService: MineService) {}

  @Post('search')
  @ApiBody({
    schema: {
      type: 'object',
      properties: {
        MINE: { type: 'string' },
        ASK2PAGE: { type: 'number' },
        PGMINE: { type: 'number', nullable: true }
      },
      required: ['MINE', 'ASK2PAGE']
    }
  })
  async searchByMine(
    @Body('MINE') mineCode: string,
    @Body('ASK2PAGE') ask2Page: number,
    @Body('PGMINE') pgId?: number,
    @Res() res: Response
  ) {
    try {
      if (!mineCode) {
        throw new BadRequestException('Le code Type Mine est requis');
      }

      if (ask2Page === 1) {
        const result = await this.mineService.findCarByMineCode(mineCode);
        return res.redirect(301, result.url);
      }

      if (ask2Page === 2 && pgId) {
        const result = await this.mineService.findCarWithParts(mineCode, pgId);
        return res.redirect(301, result.url);
      }

      return res.redirect('/type-mine.html');

    } catch (error) {
      this.logger.error(`Erreur recherche Type Mine: ${error.message}`, error.stack);
      return res.redirect('/type-mine.html');
    }
  }
}
