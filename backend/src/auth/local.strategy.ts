import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { Strategy } from 'passport-local';
import { PrismaService } from '../prisma/prismaservices'; // Modifiez l'import selon vos besoins

@Injectable()
export class LocalStrategy extends PassportStrategy(Strategy) {
  constructor(private prisma: PrismaService) {
    super({
      usernameField: 'email', // ou 'username' si nécessaire
    });
  }

  async validate(email: string, password: string): Promise<any> {
    // Logique pour vérifier l'utilisateur
    const user = await this.prisma.user.findUnique({
      where: { email: email.toLowerCase() },
    });

    if (!user || user.password !== password) {
      throw new UnauthorizedException('Invalid credentials');
    }

    return user; // Retournons l'utilisateur si tout est OK
  }
}



