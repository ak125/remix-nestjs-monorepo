import { Module } from '@nestjs/common';
import { UsersController } from './users.controller';
import { UsersService } from './users.service';
import { PrismaService } from '../../prisma/prisma.service';
import { AuthModule } from '../auth/auth.module'; // Importation de l'authentification

@Module({
  imports: [AuthModule], // 🔹 Assure que les Guards et AuthService sont disponibles
  controllers: [UsersController],
  providers: [UsersService, PrismaService],
  exports: [UsersService], // 🔹 Export du service pour d'autres modules si nécessaire
})
export class UsersModule {}
