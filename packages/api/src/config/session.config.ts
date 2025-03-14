import { createClient } from 'redis';
import session from 'express-session';
import RedisStore from 'connect-redis';
import { z } from 'zod';

// Configuration schema with Zod
export const SessionConfigSchema = z.object({
  redisUrl: z.string().url(),
  secret: z.string().min(32),
  secure: z.boolean().default(false),
  maxAge: z.number().positive()
});

// Session configuration
export const createSessionConfig = (config: z.infer<typeof SessionConfigSchema>) => {
  const redisClient = createClient({
    url: config.redisUrl 
  });

  redisClient.on('error', err => console.error('Redis Client Error', err));
  redisClient.on('connect', () => console.log('Redis Client Connected'));

  return {
    store: new RedisStore({ 
      client: redisClient,
      prefix: 'session:'
    }),
    secret: config.secret,
    resave: false,
    saveUninitialized: false,
    cookie: {
      secure: config.secure,
      httpOnly: true,
      maxAge: config.maxAge
    }
  };
};

// Connect to Redis
export const initRedis = async (redisUrl: string) => {
  const client = createClient({ url: redisUrl });
  await client.connect();
  return client;
};
