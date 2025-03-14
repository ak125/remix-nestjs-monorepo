import { Injectable, UnauthorizedException, NotFoundException, BadRequestException } from '@nestjs/common';
import * as Redis from 'ioredis';
import * as bcrypt from 'bcrypt';
import { v4 as uuidv4 } from 'uuid';
import * as crypto from 'crypto';
import { MailerService } from '../modules/shared/mailer/mailer.service';
import { PrismaService } from '../prisma/prisma.service';

const PASSWORD_SALT = 10;

// Utiliser Redis en se connectant à l'instance Docker via `docker-compose.redis.yml`
const redisClient = new Redis({
  host: 'redis', // Nom du service Redis dans docker-compose
  port: 6379, // Port par défaut
});

@Injectable()
export class AuthService {
  constructor(
    private readonly mailerService: MailerService,
    private readonly prismaService: PrismaService,
  ) {}

  async login(email: string, password: string) {
    const user = await this.prismaService.user.findUnique({ where: { email } });

    if (!user) {
      throw new UnauthorizedException('Identifiants incorrects.');
    }

    const isPasswordValid = await bcrypt.compare(password, user.password);
    if (!isPasswordValid) {
      throw new UnauthorizedException('Mot de passe incorrect.');
    }

    const sessionId = uuidv4();
    await redisClient.set(sessionId, JSON.stringify({ id: user.id, email: user.email }), 'EX', 86400);

    return { sessionId, user: { id: user.id, email: user.email } };
  }

  async forgotPassword(email: string) {
    const user = await this.prismaService.user.findUnique({ where: { email } });

    if (!user) {
      throw new NotFoundException('Email non trouvé.');
    }

    const resetToken = crypto.randomBytes(32).toString('hex');
    await redisClient.set(`resetToken:${resetToken}`, JSON.stringify({ email }), 'EX', 3600);

    const resetLink = `http://localhost:3000/reset-password?token=${resetToken}`;
    await this.mailerService.sendMail(email, 'Réinitialisation du mot de passe', `Cliquez ici pour réinitialiser votre mot de passe : ${resetLink}`);

    return { message: 'Un email de réinitialisation a été envoyé.' };
  }

  async resetPassword(token: string, newPassword: string) {
    const storedData = await redisClient.get(`resetToken:${token}`);

    if (!storedData) {
      throw new BadRequestException('Token invalide ou expiré.');
    }

    const { email } = JSON.parse(storedData);
    const hashedPassword = await bcrypt.hash(newPassword, PASSWORD_SALT);
    await this.prismaService.user.update({ where: { email }, data: { password: hashedPassword } });

    await redisClient.del(`resetToken:${token}`);

    return { message: 'Mot de passe mis à jour avec succès.' };
  }
}
