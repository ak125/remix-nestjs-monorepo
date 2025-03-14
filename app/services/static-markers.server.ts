import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export enum MarkerType {
  PRICE_LOW = 'PRICE_LOW',
  OFFERS = 'OFFERS', 
  COMPATIBILITY = 'COMPATIBILITY'
}

export class StaticMarkersService {
  private async getMarkersByType(type: MarkerType): Promise<string[]> {
    const cacheKey = `markers:${type}`;
    const cachedMarkers = await cache.get<string[]>(cacheKey);
    
    if (cachedMarkers) {
      return cachedMarkers;
    }
    
    const markers = await prisma.staticMarker.findMany({
      where: { 
        type,
        active: true
      },
      orderBy: { position: 'asc' },
      select: { value: true }
    });
    
    const markerValues = markers.map(m => m.value);
    await cache.set(cacheKey, markerValues, 60 * 60); // 1 hour
    
    return markerValues;
  }
  
  async getPriceLowMarkers(): Promise<string[]> {
    return this.getMarkersByType(MarkerType.PRICE_LOW);
  }
  
  async getOffersMarkers(): Promise<string[]> {
    return this.getMarkersByType(MarkerType.OFFERS);
  }
  
  async getCompatibilityMarkers(): Promise<string[]> {
    return this.getMarkersByType(MarkerType.COMPATIBILITY);
  }
  
  async getRandomMarker(type: MarkerType): Promise<string> {
    const markers = await this.getMarkersByType(type);
    if (!markers.length) return '';
    
    const randomIndex = Math.floor(Math.random() * markers.length);
    return markers[randomIndex];
  }
}
