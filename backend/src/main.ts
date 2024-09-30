import { getPublicDir } from '@fafa/frontend';
import { NestFactory } from '@nestjs/core';
import { NestExpressApplication } from '@nestjs/platform-express';
import { urlencoded } from 'body-parser';
import RedisStore from 'connect-redis';
import session from 'express-session';
import Redis from 'ioredis';
import passport from 'passport';
import { AppModule } from './app.module';

// Initialize Redis client.
const redisUrl = process.env.REDIS_URL || 'redis://localhost:6379';
const redisClient = new Redis(redisUrl);

// Initialize Redis store.
const redisStore = new RedisStore({
  client: redisClient,
  ttl: 86400 * 30, // 30 days
});

async function bootstrap() {
  // Create NestJS application.
  const app = await NestFactory.create<NestExpressApplication>(AppModule, {
    bodyParser: false,
  });

  // Apply session middleware.
  app.set('trust proxy', 1);
  app.use(
    session({
      store: redisStore,
      resave: false,
      saveUninitialized: false,
      secret: process.env.SESSION_SECRET || '123',
      cookie: {
        maxAge: 1000 * 60 * 60 * 24 * 30, // 30 days
        secure: process.env.NODE_ENV === 'production', // Set to true in production
      },
    }),
  );

  // Initialize Passport.
  app.use(passport.initialize());
  app.use(passport.session());
  app.use('/auth/login', urlencoded({ extended: true })); // Add this line;
  app.use('/auth/logout', urlencoded({ extended: true })); // Add this line;

  // Serve static assets.
  app.useStaticAssets(getPublicDir(), {
    immutable: true,
    maxAge: '1y',
    index: false,
  });

  // Obtenir le port à partir de la variable d'environnement ou utiliser la valeur par défaut.
  const selectedPort = process.env.PORT || 3000;

  console.log(`Running on port http://localhost:${selectedPort}`);
  await app.listen(selectedPort);
}

// Démarrer l'application.
bootstrap();
