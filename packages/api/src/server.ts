import express from "express";
import session from "express-session";
import { createClient } from "redis";
import { z } from "zod";

const configSchema = z.object({
  redisUrl: z.string().url(),
  sessionSecret: z.string().min(32),
  port: z.number().positive()
});

const config = configSchema.parse({
  redisUrl: process.env.REDIS_URL || "redis://localhost:6379",
  sessionSecret: process.env.SESSION_SECRET || "change-me-in-production",
  port: Number(process.env.PORT) || 5000
});

const redisClient = createClient({ url: config.redisUrl });
redisClient.connect().catch(console.error);

const app = express();

app.use(session({
  store: new RedisStore({ client: redisClient }),
  secret: config.sessionSecret,
  resave: false,
  saveUninitialized: false,
  cookie: {
    secure: process.env.NODE_ENV === "production",
    httpOnly: true,
    maxAge: 24 * 60 * 60 * 1000 // 24 heures
  }
}));

// Routes API
app.use(express.json());

app.listen(config.port, () => {
  console.log(`🚀 Server ready at http://localhost:${config.port}`);
});
