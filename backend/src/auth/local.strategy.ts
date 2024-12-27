import { IStrategyOptionsWithRequest, Strategy } from 'passport-local';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class LocalStrategy extends PassportStrategy(Strategy, 'local') {
  constructor(private readonly prismaService: PrismaService) {
    super({
      passReqToCallback: true,
      usernameField: 'email',
    } as IStrategyOptionsWithRequest);
  }

  async validate(_request: Express.Request, email: string, password: string) {
    console.log('--- Début de la méthode validate ---');
    console.log('Email reçu :', email);
    console.log('Mot de passe reçu :', password);

    const existingUser = await this.prismaService.user.findUnique({
      where: {
        email: email.toLowerCase(),
      },
      select: {
        id: true,
        email: true,
        name: true,
        password: true,
      },
    });

    console.log('Utilisateur trouvé :', existingUser);

    if (!existingUser) {
      console.error('Erreur : Email incorrect');
      throw new UnauthorizedException('email incorrect');
    }

    const isPasswordTheSame = existingUser.password === password;
    console.log('Mot de passe valide :', isPasswordTheSame);

    if (!isPasswordTheSame) {
      console.error('Erreur : Mot de passe incorrect');
      throw new UnauthorizedException('mot de passe incorrect');
    }

    console.log('Utilisateur authentifié avec succès :', existingUser);
    console.log('--- Fin de la méthode validate ---');
    return existingUser;
  }
}
