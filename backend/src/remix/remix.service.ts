import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class RemixService {
  constructor(private readonly prisma:PrismaService) {}
  public readonly getHello = (): string => {
    return 'MachAllah';
  };

  public readonly getUser = async ({ userId }: { userId: string}) => {
    return await this.prisma.user.findUnique({
      where: {
        id: userId,
      },select: {
        id: true,
        email: true,
        name: true,
      },
    });
  }
}
