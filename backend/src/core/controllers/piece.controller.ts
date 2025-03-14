import { Controller, Get, Query, ParseIntPipe, CacheInterceptor, UseInterceptors } from '@nestjs/common';
import { PieceService } from '../services/piece.service';

@Controller('piece')
@UseInterceptors(CacheInterceptor)
export class PieceController {
  constructor(private readonly pieceService: PieceService) {}

  @Get('details')
  async getPieceDetails(
    @Query('piece_id', ParseIntPipe) pieceId: number
  ) {
    const piece = await this.pieceService.getPieceDetails(pieceId);
    
    // Format de réponse compatible avec l'ancien système
    return {
      title: `${piece.PIECE_NAME} ${piece.PIECE_NAME_SIDE} ${piece.PIECE_NAME_COMP} ${piece.marque.PM_NAME} ${piece.PIECE_REF}`,
      references: piece.references,
      specifications: piece.specifications,
      images: piece.images,
      brand: piece.marque,
      prices: piece.prices
    };
  }

  @Get('compatibilities')
  async getPieceCompatibilities(
    @Query('piece_id', ParseIntPipe) pieceId: number
  ) {
    return this.pieceService.getPieceCompatibilities(pieceId);
  }
}
