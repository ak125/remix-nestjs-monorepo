import { Controller, Get, Query, ParseIntPipe } from '@nestjs/common';
import { MotorisationService } from '../services/motorisation.service';

@Controller('motorisation')
export class MotorisationController {
  constructor(private readonly motorisationService: MotorisationService) {}

  @Get()
  getMotorisations(
    @Query('formCarMarqueid', ParseIntPipe) marqueId: number,
    @Query('formCarMarqueYear', ParseIntPipe) marqueYear: number, 
    @Query('formCarModelid', ParseIntPipe) modelId: number,
    @Query('formGammeid', ParseIntPipe) gammeId: number
  ) {
    return this.motorisationService.getMotorisations({
      marqueId,
      marqueYear,
      modelId,
      gammeId
    });
  }
}
