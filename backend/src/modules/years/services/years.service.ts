import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { CACHE_MANAGER, Inject } from '@nestjs/common';
import { Cache } from 'cache-manager';

@Injectable()
export class YearsService {
  private readonly logger = new Logger(YearsService.name);
  private readonly currentYear = new Date().getFullYear();

  constructor(
    private readonly prisma: PrismaService,
    @Inject(CACHE_MANAGER) private cacheManager: Cache
  ) {}

  async getYearsForMarque(marqueId: number) {
    const cacheKey = `years:${marqueId}`;
    
    // Check cache first
    const cached = await this.cacheManager.get<any>(cacheKey);
    if (cached) {
      return cached;
    }

    const [minYear, maxYear] = await Promise.all([
      this.getMinYear(marqueId),
      this.getMaxYear(marqueId)
    ]);

    if (!minYear || !maxYear) {
      return [{ value: 0, label: 'Année' }];
    }

    const years = this.generateYearsList(minYear, maxYear);
    
    // Cache results
    await this.cacheManager.set(cacheKey, years, { ttl: 3600 });
    
    return years;
  }

  private async getMinYear(marqueId: number): Promise<number | null> {
    const result = await this.prisma.modele.aggregate({
      where: { 
        marqueId,
        display: true 
      },
      _min: { yearFrom: true }
    });
    return result._min.yearFrom;
  }

  private async getMaxYear(marqueId: number): Promise<number> {
    const result = await this.prisma.modele.aggregate({
      where: { 
        marqueId,
        display: true 
      },
      _max: { yearTo: true }
    });
    return result._max.yearTo || this.currentYear;
  }

  private generateYearsList(minYear: number, maxYear: number) {
    const years = [];
    const favoriteYears = [2020, 2010, 2000];

    for (let year = maxYear; year >= minYear; year--) {
      years.push({
        value: year,
        label: year.toString(),
        favorite: favoriteYears.includes(year)
      });
    }

    return years;
  }
}
