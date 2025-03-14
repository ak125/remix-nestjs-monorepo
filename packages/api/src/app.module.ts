import { Module, NestModule, MiddlewareConsumer } from '@nestjs/common';
import * as session from 'express-session';
import { sessionConfig, initRedis } from './config/session.config';
import { AuthMiddleware } from './middleware/auth.middleware';
import { SessionMiddleware } from './middleware/session.middleware';
import { z } from 'zod';

// Validation du config
const envSchema = z.object({
  REDIS_URL: z.string().url(),
  SESSION_SECRET: z.string().min(32),
});

// Validate environment variables
const env = envSchema.parse(process.env);

@Module({
  imports: [
    // ...existing imports...
  ],
  controllers: [
    // ...existing controllers...
  ],
  providers: [
    {
      provide: 'REDIS_CLIENT',
      useFactory: async () => await initRedis()
    }
  ]
})
export class AppModule implements NestModule {
  configure(consumer: MiddlewareConsumer) {
    consumer
      .apply(session(sessionConfig))
      .forRoutes('*');
    
    consumer
      .apply(AuthMiddleware)
      .exclude('auth/login', 'auth/register')
      .forRoutes('*');

    consumer
      .apply(SessionMiddleware)
      .forRoutes('*');
  }
}
