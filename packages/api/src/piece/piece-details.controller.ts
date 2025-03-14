import { Controller, Get, Param, Query, UseGuards } from '@nestjs/common';
import { PieceDetailsService } from './piece-details.service';
import { ApiKeyGuard } from '../auth/guards/api-key.guard';
import { CacheInterceptor } from '@nestjs/cache-manager';
import { UseInterceptors } from '@nestjs/common';

@Controller('pieces')
@UseGuards(ApiKeyGuard)
@UseInterceptors(CacheInterceptor)
export class PieceDetailsController {
  constructor(private pieceDetails: PieceDetailsService) {}

  @Get(':id')
  async getPieceDetails(
    @Param('id') pieceId: string,
    @Query('includeHistory') includeHistory?: boolean
  ) {
    return this.pieceDetails.getPieceDetails({
      pieceId,
      includeHistory
    });
  }
}
