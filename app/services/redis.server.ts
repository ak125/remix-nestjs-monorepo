import Redis from "ioredis";
import { singleton } from "~/lib/singleton.server";

// Singleton pour la connexion Redis
export const redis = singleton("redis", () => {
  const redisUrl = process.env.REDIS_URL || "redis://localhost:6379";
  
  const client = new Redis(redisUrl, {
    maxRetriesPerRequest: 3,
    enableReadyCheck: true,
    reconnectOnError: (err) => {
      const targetError = "READONLY";
      if (err.message.includes(targetError)) {
        return true;
      }
      return false;
    },
  });
  
  client.on("error", (error) => {
    console.error(`Redis connection error: ${error}`);
  });
  
  client.on("connect", () => {
    console.log("Connected to Redis");
  });
  
  return client;
});

// Classe pour la gestion des sessions Redis
export class RedisSessionService {
  private readonly prefix = "session:";
  private readonly defaultExpiry = 60 * 60 * 24 * 7; // 7 days in seconds
  
  // Créer ou mettre à jour une session
  async setSession(sessionId: string, data: Record<string, any>, expiry = this.defaultExpiry): Promise<void> {
    const key = this.prefix + sessionId;
    await redis.set(key, JSON.stringify(data), "EX", expiry);
  }
  
  // Récupérer les données d'une session
  async getSession(sessionId: string): Promise<Record<string, any> | null> {
    const key = this.prefix + sessionId;
    const data = await redis.get(key);
    if (!data) return null;
    
    try {
      return JSON.parse(data);
    } catch (error) {
      console.error(`Error parsing session data: ${error}`);
      return null;
    }
  }
  
  // Supprimer une session
  async deleteSession(sessionId: string): Promise<void> {
    const key = this.prefix + sessionId;
    await redis.del(key);
  }
  
  // Prolonger la durée d'une session existante
  async extendSession(sessionId: string, expiry = this.defaultExpiry): Promise<boolean> {
    const key = this.prefix + sessionId;
    const exists = await redis.exists(key);
    if (!exists) return false;
    
    await redis.expire(key, expiry);
    return true;
  }
  
  // Récupérer et mettre à jour une session en une seule opération
  async getAndUpdateSession(
    sessionId: string,
    updater: (data: Record<string, any>) => Record<string, any>,
    expiry = this.defaultExpiry
  ): Promise<Record<string, any> | null> {
    const session = await this.getSession(sessionId);
    if (!session) return null;
    
    const updatedSession = updater(session);
    await this.setSession(sessionId, updatedSession, expiry);
    
    return updatedSession;
  }
}

// Exporter une instance par défaut du service
export const redisSessionService = new RedisSessionService();
