import { Injectable } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { Strategy as AppleStrategy } from 'passport-apple';
import { Strategy as FacebookStrategy } from 'passport-facebook';
import { PrismaService } from '../../prisma/prisma.service';

@Injectable()
export class SocialAuthStrategies {
  constructor(private prisma: PrismaService) {}

  @Injectable()
  export class AppleAuthStrategy extends PassportStrategy(AppleStrategy, 'apple') {
    constructor() {
      super({
        clientID: process.env.APPLE_CLIENT_ID!,
        teamID: process.env.APPLE_TEAM_ID!,
        keyID: process.env.APPLE_KEY_ID!,
        key: process.env.APPLE_PRIVATE_KEY!.replace(/\\n/g, '\n'),
        callbackURL: `${process.env.API_URL}/auth/apple/callback`,
        scope: ['email', 'name'],
        passReqToCallback: true
      });
    }

    async validate(req: any, token: string, refreshToken: string, profile: any) {
      const email = profile.email;
      
      let user = await this.prisma.user.findUnique({
        where: { email }
      });

      if (!user) {
        user = await this.prisma.user.create({
          data: {
            email,
            name: profile.name?.givenName || 'Apple User',
            provider: 'apple'
          }
        });
      }

      return user;
    }
  }

  @Injectable()
  export class FacebookAuthStrategy extends PassportStrategy(FacebookStrategy, 'facebook') {
    constructor() {
      super({
        clientID: process.env.FACEBOOK_CLIENT_ID!,
        clientSecret: process.env.FACEBOOK_CLIENT_SECRET!,
        callbackURL: `${process.env.API_URL}/auth/facebook/callback`,
        profileFields: ['id', 'emails', 'name', 'picture.type(large)'],
        scope: ['email', 'public_profile']
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
            name: profile.name.givenName + ' ' + profile.name.familyName,
            imageUrl: profile.photos?.[0]?.value,
            provider: 'facebook'
          }
        });
      }

      return user;
    }
  }
}
