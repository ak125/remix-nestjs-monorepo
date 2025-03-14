import { Controller, Get, Session, UnauthorizedException } from '@nestjs/common';
import { AuthService } from './auth.service';

@Controller('auth')
export class AuthController {
  constructor(private authService: AuthService) {}

  @Get('verify') 
  async verifySession(@Session() session: Record<string, any>) {
    if (!session) {
      throw new UnauthorizedException('No session found');
    }

    return this.authService.verifySession(session);
  }
}
