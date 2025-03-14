import { Controller, Get, Query, ParseIntPipe } from '@nestjs/common';
import { FicheService } from './fiche.service';

@Controller('fiche')
export class FicheController {
  constructor(private readonly ficheService: FicheService) {}

  @Get()
  async getPieceDetails(
    @Query('piece_id', ParseIntPipe) pieceId: number,
    @Query('type_id', ParseIntPipe) typeId?: number
  ) {
    return this.ficheService.getPieceDetails({
      pieceId,
      typeId
    });
  }
}
