import { Injectable, NestMiddleware } from '@nestjs/common';
import { Request, Response, NextFunction } from 'express';
import session from 'express-session';
import RedisStore from 'connect-redis';
import { createClient } from 'redis';

declare module 'express-session' {
  interface SessionData {
    myaklog: boolean;
    myakciv: string;
    myakprenom: string;
    myaknom: string;
    amcnkCart: {
      id_article: number[];
    };
  }
}

const redisClient = createClient({ 
  url: process.env.REDIS_URL || 'redis://localhost:6379' 
});

redisClient.connect().catch(console.error);

@Injectable() 
export class SessionMiddleware implements NestMiddleware {
  use(req: Request, res: Response, next: NextFunction) {
    session({
      store: new RedisStore({ client: redisClient }),
      secret: process.env.SESSION_SECRET || 'super_secret_key',
      resave: false,
      saveUninitialized: false,
      cookie: {
        secure: process.env.NODE_ENV === 'production',
        httpOnly: true,
        maxAge: 24 * 60 * 60 * 1000 // 1 jour
      },
    })(req, res, next);
  }
}
