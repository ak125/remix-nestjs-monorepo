import NodeCache from 'node-cache';

// Simple in-memory cache
const memoryCache = new NodeCache({
  stdTTL: 3600, // 1 hour default
  checkperiod: 600 // check for expired keys every 10 minutes
});

export const cache = {
  async get<T>(key: string): Promise<T | undefined> {
    return memoryCache.get<T>(key);
  },
  
  async set<T>(key: string, value: T, ttl: number = 3600): Promise<boolean> {
    return memoryCache.set(key, value, ttl);
  },
  
  async del(key: string): Promise<number> {
    return memoryCache.del(key);
  },
  
  async flush(): Promise<void> {
    memoryCache.flushAll();
  }
};
