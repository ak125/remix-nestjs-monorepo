import { Controller, Get, Req, Res, UnauthorizedException } from '@nestjs/common';
import { SessionService } from './session.service';
import { Request, Response } from 'express';

@Controller('session')
export class SessionController {
  constructor(private readonly sessionService: SessionService) {}

  @Get('check')
  async checkSession(@Req() req: Request, @Res() res: Response) {
    const sessionId = req.cookies.sessionId;

    try {
      const session = await this.sessionService.checkSession(sessionId);
      return res.json({
        valid: true,
        user: session,
      });
    } catch (error) {
      if (error instanceof UnauthorizedException) {
        return res.json({
          valid: false,
          reason: 'Session invalide ou expirée',
        });
      }
      throw error;
    }
  }
}
