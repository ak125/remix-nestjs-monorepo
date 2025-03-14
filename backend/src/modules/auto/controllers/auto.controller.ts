import { Controller, Get, Query, UseGuards, CacheInterceptor, UseInterceptors } from '@nestjs/common';
import { AutoService } from '../services/auto.service';
import { AuthGuard } from '../../../common/guards/auth.guard';
import { ApiTags, ApiQuery } from '@nestjs/swagger';

@ApiTags('Auto')
@Controller('auto')
@UseInterceptors(CacheInterceptor)
export class AutoController {
  constructor(private readonly autoService: AutoService) {}

  @Get('vehicle-parts')
  @ApiQuery({ name: 'marqueId', required: true })
  @ApiQuery({ name: 'modeleId', required: true })
  @ApiQuery({ name: 'typeId', required: true })
  @ApiQuery({ name: 'gammeId', required: true })
  async getVehicleParts(
    @Query('marqueId') marqueId: string,
    @Query('modeleId') modeleId: string,
    @Query('typeId') typeId: string,
    @Query('gammeId') gammeId: string,
  ) {
    return this.autoService.getVehicleWithParts({
      marqueId,
      modeleId,
      typeId,
      gammeId,
    });
  }

  @Get('filters')
  @UseGuards(AuthGuard)
  async getFilters(
    @Query('typeId') typeId: string,
    @Query('gammeId') gammeId: string,
  ) {
    return this.autoService.getFilters(typeId, gammeId);
  }
}
