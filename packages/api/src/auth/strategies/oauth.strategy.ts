import { Injectable } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { Strategy as GoogleStrategy } from 'passport-google-oauth20';
import { Strategy as GithubStrategy } from 'passport-github2';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class AuthStrategies {
  constructor(private prisma: PrismaService) {}

  @Injectable()
  export class GoogleOAuthStrategy extends PassportStrategy(GoogleStrategy, 'google') {
    constructor() {
      super({
        clientID: process.env.GOOGLE_CLIENT_ID!,
        clientSecret: process.env.GOOGLE_CLIENT_SECRET!,
        callbackURL: `${process.env.API_URL}/auth/google/callback`,
        scope: ['email', 'profile']
      });
    }

    async validate(accessToken: string, refreshToken: string, profile: any) {
      const email = profile.emails[0].value;
      
      let user = await this.prisma.user.findUnique({
        where: { email }
      });

      if (!user) {
        user = await this.prisma.user.create({
          data: {
            email,
            name: profile.displayName,
            imageUrl: profile.photos[0]?.value
          }
        });
      }

      return user;
    }
  }

  @Injectable()
  export class GithubOAuthStrategy extends PassportStrategy(GithubStrategy, 'github') {
    constructor() {
      super({
        clientID: process.env.GITHUB_CLIENT_ID!,
        clientSecret: process.env.GITHUB_CLIENT_SECRET!,
        callbackURL: `${process.env.API_URL}/auth/github/callback`,
        scope: ['user:email']
      });
    }

    async validate(accessToken: string, refreshToken: string, profile: any) {
      const email = profile.emails[0].value;
      
      let user = await this.prisma.user.findUnique({
        where: { email }
      });

      if (!user) {
        user = await this.prisma.user.create({
          data: {
            email,
            name: profile.displayName,
            imageUrl: profile.photos[0]?.value
          }
        });
      }

      return user;
    }
  }
}
