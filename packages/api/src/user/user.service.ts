import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { z } from 'zod';

const updateSchema = z.object({
  name: z.string().min(2).optional(),
  email: z.string().email().optional(),
  imageUrl: z.string().url().optional(),
  preferences: z.record(z.unknown()).optional()
});

@Injectable()
export class UserService {
  constructor(private prisma: PrismaService) {}

  async getUser(id: string) {
    const user = await this.prisma.user.findUnique({
      where: { id },
      select: {
        id: true,
        name: true,
        email: true,
        imageUrl: true,
        role: true,
        preferences: true,
        createdAt: true
      }
    });

    if (!user) {
      throw new NotFoundException(`User ${id} not found`);
    }

    return user;
  }

  async updateUser(id: string, data: unknown) {
    const user = await this.getUser(id);
    const validated = updateSchema.parse(data);

    if (validated.email && validated.email !== user.email) {
      const existing = await this.prisma.user.findUnique({
        where: { email: validated.email }
      });

      if (existing) {
        throw new BadRequestException('Email already taken');
      }
    }

    return this.prisma.user.update({
      where: { id },
      data: validated
    });
  }

  async getUserPreferences(id: string) {
    const user = await this.getUser(id);
    return user.preferences || {};
  }

  async updateUserPreferences(id: string, preferences: Record<string, unknown>) {
    return this.prisma.user.update({
      where: { id },
      data: { preferences }
    });
  }
}
