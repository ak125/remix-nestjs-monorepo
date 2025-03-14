import { Controller, Get, Query, Res, UseInterceptors, CacheInterceptor } from '@nestjs/common';
import { Response } from 'express';
import { GammeService } from '../services/gamme.service';
import { ApiTags, ApiQuery } from '@nestjs/swagger';

@ApiTags('Gamme')
@Controller('gamme')
@UseInterceptors(CacheInterceptor)
export class GammeController {
  constructor(private readonly gammeService: GammeService) {}

  @Get()
  @ApiQuery({ name: 'pg_id', required: true })
  async getGammeData(
    @Query('pg_id') pgId: string, 
    @Res() res: Response
  ) {
    // Redirection spéciale
    if (Number(pgId) === 3940) {
      return res.redirect(301, '/pieces/corps-papillon-158.html');
    }

    const [gammePrivilege, gammeData] = await Promise.all([
      this.gammeService.validateGammeAccess(pgId),
      this.gammeService.getGammeWithRelations(pgId)
    ]);

    if (!gammePrivilege) {
      return res.status(410).render('410.page');
    }

    if (!gammePrivilege.display) {
      return res.status(412).render('412.page', { pageDisabled: 'z' });
    }

    return res.render('gamme', {
      gamme: gammeData.details,
      seo: gammeData.seo,
      stats: gammeData.stats,
      blog: gammeData.blog
    });
  }
}
