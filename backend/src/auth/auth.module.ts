import { Module } from '@nestjs/common';
import { PassportModule } from '@nestjs/passport';
import { PrismaService } from '../prisma/prisma.services'; // Assurez-vous que le chemin est correct
import { AuthController } from './auth.controller'; // Importer le contrôleur d'authentification
import { AuthService } from './auth.service';
import { CookieSerializer } from './cookie-serializer';
import { LocalAuthGuard } from './local-auth.guard';
import { LocalStrategy } from './local.strategy';

@Module({
  imports: [
    PassportModule.register({
      defaultStrategy: 'local',
      property: 'user',
      session: true,
    }),
  ],
  controllers: [AuthController], // Ajouter le contrôleur ici
  providers: [
    LocalStrategy,
    LocalAuthGuard,
    CookieSerializer,
    PrismaService,
    AuthService,
  ],
  exports: [AuthService],
})
export class AuthModule {}
