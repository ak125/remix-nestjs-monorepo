import { Injectable, UnauthorizedException } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { Redis } from 'ioredis';

@Injectable()
export class SessionService {
  private readonly redisClient: Redis;
  private readonly SESSION_PREFIX = 'session:';

  constructor(private readonly configService: ConfigService) {
    this.redisClient = new Redis({
      host: this.configService.get('REDIS_HOST', 'localhost'),
      port: this.configService.get('REDIS_PORT', 6379),
      password: this.configService.get('REDIS_PASSWORD'),
    });
  }

  async checkSession(sessionId: string) {
    if (!sessionId) {
      throw new UnauthorizedException('Aucune session trouvée');
    }

    const sessionData = await this.redisClient.get(
      `${this.SESSION_PREFIX}${sessionId}`
    );

    if (!sessionData) {
      throw new UnauthorizedException('Session expirée');
    }

    try {
      return JSON.parse(sessionData);
    } catch (error) {
      console.error('Erreur lors du parsing de la session:', error);
      throw new UnauthorizedException('Session invalide');
    }
  }

  async onApplicationShutdown() {
    await this.redisClient.quit();
  }
}
