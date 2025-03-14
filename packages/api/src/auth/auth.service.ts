import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { createCipheriv, randomBytes } from 'crypto';
import { HashService } from '../hash/hash.service';
import { z } from 'zod';

const loginSchema = z.object({
  email: z.string().email(),
  password: z.string(),
  device: z.string().optional(),
  ipAddress: z.string().optional()
});

@Injectable()
export class AuthService {
  constructor(
    private prisma: PrismaService,
    private hash: HashService
  ) {}

  async createSession(userId: string) {
    const sessionToken = this.generateToken();

    const session = await this.prisma.session.create({
      data: {
        id: sessionToken,
        userId,
        expiresAt: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000) // 1 semaine
      }
    });

    return session;
  }

  private generateToken(): string {
    const key = Buffer.from(process.env.SESSION_SECRET!, 'utf8');
    const iv = randomBytes(16);
    const cipher = createCipheriv('aes-256-gcm', key, iv);
    const token = cipher.update(new Date().toISOString(), 'utf8', 'hex');
    return `${token}${cipher.final('hex')}`;
  }

  async createSocialAuthSession(data: { 
    email: string, 
    provider: string, 
    providerAccountId: string 
  }) {
    const user = await this.prisma.user.findUnique({
      where: { email: data.email }
    });

    if (!user) {
      throw new Error('User not found');
    }

    // Create or update provider account link
    await this.prisma.account.upsert({
      where: {
        provider_providerAccountId: {
          provider: data.provider,
          providerAccountId: data.providerAccountId
        }
      },
      update: {},
      create: {
        userId: user.id,
        provider: data.provider,
        providerAccountId: data.providerAccountId,
        type: 'oauth'
      }
    });

    return this.createSession(user.id);
  }

  async login(data: unknown) {
    const validated = loginSchema.parse(data);

    const user = await this.prisma.user.findUnique({
      where: { email: validated.email }
    });

    if (!user || !user.isActive) {
      throw new UnauthorizedException('Invalid credentials');
    }

    const isValid = await this.hash.compare(validated.password, user.password);
    if (!isValid) {
      throw new UnauthorizedException('Invalid credentials');
    }

    return this.prisma.$transaction(async (tx) => {
      // Create session
      const session = await tx.userSession.create({
        data: {
          userId: user.id,
          token: crypto.randomUUID(),
          expiresAt: new Date(Date.now() + 24 * 60 * 60 * 1000),
          device: validated.device,
          ipAddress: validated.ipAddress
        }
      });

      // Log activity
      await tx.userActivity.create({
        data: {
          userId: user.id,
          type: 'login',
          details: {
            device: validated.device,
            ipAddress: validated.ipAddress
          }
        }
      });

      return {
        user: {
          id: user.id,
          email: user.email,
          name: user.name,
          role: user.role
        },
        session: {
          token: session.token,
          expiresAt: session.expiresAt
        }
      };
    });
  }

  async logout(sessionToken: string) {
    const session = await this.prisma.userSession.findUnique({
      where: { token: sessionToken },
      include: { user: true }
    });

    if (!session) return;

    await this.prisma.$transaction([
      this.prisma.userSession.delete({
        where: { token: sessionToken }
      }),
      this.prisma.userActivity.create({
        data: {
          userId: session.userId,
          type: 'logout',
          details: {
            device: session.device,
            ipAddress: session.ipAddress
          }
        }
      })
    ]);
  }
}
