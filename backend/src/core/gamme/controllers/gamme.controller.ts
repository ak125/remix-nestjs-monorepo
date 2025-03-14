import { Controller, Get, Param, ParseIntPipe } from '@nestjs/common';
import { GammeService } from '../services/gamme.service';

@Controller('gamme')
export class GammeController {
  constructor(private readonly gammeService: GammeService) {}

  @Get('piece/:id')
  async getPiece(@Param('id', ParseIntPipe) id: number) {
    return this.gammeService.getPieceById(id);
  }
}
