import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { VehicleService } from './vehicle.service';
import { PartsService } from './parts.service';

interface GetVehiclePartsOptions {
  marqueId: string;
  modeleId: string;
  typeId: string;
  gammeId: string;
}

@Injectable()
export class AutoService {
  constructor(
    private readonly prisma: PrismaService,
    private readonly vehicleService: VehicleService,
    private readonly partsService: PartsService,
  ) {}

  async getVehicleWithParts(options: GetVehiclePartsOptions) {
    const [vehicle, parts] = await Promise.all([
      this.vehicleService.getVehicleInfo(
        options.marqueId,
        options.modeleId,
        options.typeId
      ),
      this.partsService.getCompatibleParts(
        options.typeId,
        options.gammeId
      ),
    ]);

    return {
      vehicle,
      parts,
      total: parts.length,
      minPrice: parts.length ? Math.min(...parts.map(p => p.price)) : 0,
      maxPrice: parts.length ? Math.max(...parts.map(p => p.price)) : 0,
    };
  }

  async getFilters(typeId: string, gammeId: string) {
    return this.partsService.getAvailableFilters(typeId, gammeId);
  }
}
