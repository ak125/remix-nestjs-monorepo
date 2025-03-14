import { Injectable } from '@nestjs/common';
import Redis from 'ioredis';

@Injectable()
export class CacheService {
  private redis = new Redis(process.env.REDIS_URL);

  async get(key: string): Promise<string | null> {
    return this.redis.get(key);
  }

  async set(key: string, value: string, ttl = 3600): Promise<void> {
    await this.redis.setex(key, ttl, value);
  }
}
