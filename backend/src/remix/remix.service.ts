import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.services';

@Injectable()
export class RemixService {
  constructor(private readonly prisma: PrismaService) {}

  // Correction de la méthode getHello
  public readonly getHello = (): string => {
    return 'massdoc incroyable';
  };

  // Correction de la méthode getUser
  public readonly getUser = async ({ userId }: { userId: string }) => {
    return await this.prisma.user.findUnique({
      where: {
        id: userId,
      },
      select: {
        id: true,
        email: true,
        name: true,
      },
    });
  };
}
