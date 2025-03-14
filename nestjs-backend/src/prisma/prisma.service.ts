import { Injectable, OnModuleInit, OnModuleDestroy } from '@nestjs/common';
import { PrismaClient } from '@prisma/client';
import { CacheService } from '../cache/cache.service';

@Injectable()
export class PrismaService extends PrismaClient implements OnModuleInit, OnModuleDestroy {
  constructor(private readonly cacheService: CacheService) {
    super({
      log: process.env.NODE_ENV === 'development' ? ['query', 'error', 'warn'] : ['error'],
    });
  }

  async onModuleInit() {
    await this.$connect();
  }

  async onModuleDestroy() {
    await this.$disconnect();
  }

  /**
   * Récupère les données du cache si elles existent, sinon exécute la requête Prisma
   * et met le résultat en cache avant de le retourner
   */
  async getCached<T>(key: string, fetchFn: () => Promise<T>, ttl?: number): Promise<T> {
    return this.cacheService.getOrSet(key, fetchFn, ttl);
  }

  /**
   * Invalide le cache pour une clé donnée
   */
  async invalidateCache(key: string): Promise<void> {
    await this.cacheService.del(key);
  }

  /**
   * Invalide le cache pour toutes les clés commençant par un préfixe
   */
  async invalidateCachePrefix(prefix: string): Promise<void> {
    // Cette méthode nécessite une implémentation spécifique au client Redis,
    // nous nous contenterons d'une version simplifiée ici qui réinitialise tout le cache
    // Dans un environnement de production, on utiliserait plutôt la commande SCAN de Redis
    await this.cacheService.reset();
  }

  // Helper pour nettoyer la base pour les tests
  async cleanDatabase() {
    if (process.env.NODE_ENV !== 'test') {
      return;
    }
    
    const models = Reflect.ownKeys(this).filter(
      (key) => key[0] !== '_' && key[0] !== '$' && key !== 'constructor',
    );
    
    return Promise.all(
      models.map((modelKey) => this[modelKey as string].deleteMany()),
    );
  }
}
