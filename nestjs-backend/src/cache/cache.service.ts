import { Injectable, Inject, CACHE_MANAGER } from '@nestjs/common';
import { Cache } from 'cache-manager';

@Injectable()
export class CacheService {
  constructor(@Inject(CACHE_MANAGER) private readonly cache: Cache) {}

  async get<T>(key: string): Promise<T | undefined> {
    return this.cache.get<T>(key);
  }

  async set(key: string, value: any, ttl?: number): Promise<void> {
    return this.cache.set(key, value, ttl);
  }

  async del(key: string): Promise<void> {
    return this.cache.del(key);
  }

  async reset(): Promise<void> {
    return this.cache.reset();
  }

  /**
   * Récupère les données du cache si elles existent, sinon exécute la fonction de récupération
   * et met le résultat en cache avant de le retourner
   */
  async getOrSet<T>(key: string, fetchFn: () => Promise<T>, ttl?: number): Promise<T> {
    const cachedData = await this.get<T>(key);
    
    if (cachedData !== undefined) {
      return cachedData;
    }
    
    const freshData = await fetchFn();
    await this.set(key, freshData, ttl);
    return freshData;
  }

  /**
   * Crée une clé de cache formatée avec un préfixe et des paramètres
   */
  createKey(prefix: string, params: Record<string, any>): string {
    const sortedParams = Object.entries(params)
      .filter(([_, value]) => value !== undefined && value !== null)
      .sort(([keyA], [keyB]) => keyA.localeCompare(keyB))
      .map(([key, value]) => `${key}:${value}`)
      .join(':');
    
    return `${prefix}:${sortedParams}`;
  }
}
