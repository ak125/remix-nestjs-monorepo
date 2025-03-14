import { Module } from '@nestjs/common';
import { SecurityController } from './security.controller';
import { SecurityService } from './security.service';
import { PrismaService } from '../prisma/prisma.service';
import { SessionModule } from 'nestjs-session';

@Module({
  imports: [
    SessionModule.forRoot({
      session: {
        secret: process.env.SESSION_SECRET!,
        resave: false,
        saveUninitialized: false,
        cookie: {
          secure: process.env.NODE_ENV === 'production',
          httpOnly: true,
          maxAge: 1000 * 60 * 60 * 24 * 7 // 1 semaine
        }
      }
    })
  ],
  controllers: [SecurityController],
  providers: [SecurityService, PrismaService],
})
export class SecurityModule {}
