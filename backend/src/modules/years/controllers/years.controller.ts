import { Controller, Get, Query, UseInterceptors, CacheInterceptor } from '@nestjs/common';
import { YearsService } from '../services/years.service';
import { ApiTags, ApiQuery } from '@nestjs/swagger';

@ApiTags('Years')
@Controller('years')
@UseInterceptors(CacheInterceptor)
export class YearsController {
  constructor(private readonly yearsService: YearsService) {}

  @Get()
  @ApiQuery({ name: 'marqueId', required: true })
  async getYears(@Query('marqueId') marqueId: string) {
    const years = await this.yearsService.getYearsForMarque(Number(marqueId));

    return {
      years,
      total: years.length,
      hasData: years.length > 1
    };
  }
}
