import { Controller, Get, Param, Redirect, Query, ParseIntPipe, DefaultValuePipe } from '@nestjs/common';
import { PiecesService } from './pieces.service';

@Controller('pieces')
export class PiecesController {
  constructor(private readonly piecesService: PiecesService) {}

  @Get(':pg_id')
  async getPiece(@Param('pg_id') pg_id: string) {
    return this.piecesService.getPieceById(Number(pg_id));
  }

  @Get('redirect/:pg_id')
  @Redirect()
  async redirectPiece(@Param('pg_id') pg_id: string) {
    if (pg_id === '3940') {
      return { url: '/pieces/corps-papillon-158.html', statusCode: 301 };
    }
    return { url: `/pieces/${pg_id}`, statusCode: 200 };
  }

  @Get('search')
  async searchPieces(
    @Query('query') query: string,
    @Query('gammeId', new DefaultValuePipe(0), ParseIntPipe) gammeId?: number,
    @Query('equipementId', new DefaultValuePipe(0), ParseIntPipe) equipementId?: number,
    @Query('page', new DefaultValuePipe(1), ParseIntPipe) page?: number,
    @Query('limit', new DefaultValuePipe(20), ParseIntPipe) limit?: number,
  ) {
    return this.piecesService.searchPieces({
      query,
      gammeId: gammeId || undefined,
      equipementId: equipementId || undefined,
      page,
      limit
    });
  }

  @Get('search/filters')
  async getSearchFilters(
    @Query('query') query: string
  ) {
    return this.piecesService.getSearchFilters(query);
  }
}
