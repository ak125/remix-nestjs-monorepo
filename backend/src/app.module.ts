import { Module } from '@nestjs/common';
import { AuthController } from './auth/auth.controller'; // Import the AuthController class
import { AuthModule } from './auth/auth.module';
import { PrismaService } from './prisma/prismaservices';
import { RemixController } from './remix/remix.controller';
import { RemixService } from './remix/remix.service';

@Module({
  imports: [AuthModule],
  controllers: [AuthController, RemixController],
  providers: [PrismaService, RemixService],
})
export class AppModule {}
