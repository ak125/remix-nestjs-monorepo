import { createClient } from "redis";

// Initialisation du client Redis avec gestion d'erreur
let redisClient: ReturnType<typeof createClient>;

declare global {
  var __redisClient: ReturnType<typeof createClient> | undefined;
}

// Éviter de créer plusieurs connexions en mode développement avec HMR
if (process.env.NODE_ENV === "production") {
  redisClient = createClient({
    url: process.env.REDIS_URL || "redis://localhost:6379",
  });
} else {
  if (!global.__redisClient) {
    global.__redisClient = createClient({
      url: process.env.REDIS_URL || "redis://localhost:6379",
    });
    global.__redisClient.connect().catch(console.error);
  }
  redisClient = global.__redisClient;
}

// Gestion d'événements Redis
redisClient.on("error", (err) => console.error("Redis error:", err));
redisClient.on("connect", () => console.log("Redis connected"));

// Fonction pour assurer la connexion avant utilisation
async function getRedisClient() {
  if (!redisClient.isOpen) {
    await redisClient.connect();
  }
  return redisClient;
}

export { getRedisClient, redisClient };
