import { Controller, Post, Body, Headers, Ip } from '@nestjs/common';
import { AuthService } from './auth.service';

@Controller('auth')
export class AuthController {
  constructor(private auth: AuthService) {}

  @Post('login')
  async login(
    @Body() body: unknown,
    @Headers('user-agent') userAgent: string,
    @Ip() ip: string
  ) {
    return this.auth.login({
      ...body,
      device: userAgent,
      ipAddress: ip
    });
  }

  @Post('logout')
  async logout(@Headers('authorization') auth: string) {
    if (!auth?.startsWith('Bearer ')) return;
    const token = auth.slice(7);
    await this.auth.logout(token);
    return { success: true };
  }
}
