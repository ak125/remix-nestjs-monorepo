import { Controller, Get, Post, Req, Res, Body, UnauthorizedException, BadRequestException } from '@nestjs/common';
import { AuthService } from './auth.service';
import { Request, Response } from 'express';

@Controller('auth')
export class AuthController {
  constructor(private readonly authService: AuthService) {}

  /**
   * ✅ Vérifier l'accès d'un utilisateur via la session Redis
   */
  @Get('check-access')
  async checkAccess(@Req() req: Request, @Res() res: Response) {
    const sessionId = req.cookies.sessionId;

    if (!sessionId) {
      return this.accessDenied(res);
    }

    try {
      const session = await this.authService.checkSession(sessionId);
      return res.json(session);
    } catch (error) {
      return this.accessDenied(res);
    }
  }

  /**
   * ✅ Connexion utilisateur (Similaire à `login.php`)
   */
  @Post('login')
  async login(@Body() body: any, @Res() res: Response) {
    const { email, password } = body;

    if (!email || !password) {
      throw new BadRequestException("L'email et le mot de passe sont requis.");
    }

    const session = await this.authService.login(email, password);

    // Stocke la session dans un cookie HTTP sécurisé
    res.cookie('sessionId', session.sessionId, { httpOnly: true, secure: true });

    return res.json({ message: "Connexion réussie", session });
  }

  /**
   * ❌ Refus d'accès (Simulation de la page PHP en HTML)
   */
  private accessDenied(res: Response) {
    return res.status(401).send(`
      <!doctype html>
      <html lang="fr">
      <head>
        <meta charset="utf-8">
        <title>Accès Refusé</title>
        <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
          body { text-align: center; padding: 50px; background-color: #f8f9fa; }
          .error-container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
          h1 { color: #dc3545; }
        </style>
      </head>
      <body>
        <div class="error-container">
          <h1>⛔ Accès Refusé</h1>
          <p>Vous n'êtes pas autorisé à accéder à ce module.</p>
          <p>Si cette opération dure trop longtemps, <a href="/">cliquez ici</a> pour être redirigé.</p>
        </div>
      </body>
      </html>
    `);
  }
}
