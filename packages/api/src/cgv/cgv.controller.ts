import { Controller, Get, Post, Body, Query, Res } from '@nestjs/common';
import { Response } from 'express';
import { CGVService } from './cgv.service';
import { EmailService } from '../email/email.service';

@Controller('cgv')
export class CGVController {
  constructor(
    private readonly cgvService: CGVService,
    private readonly emailService: EmailService
  ) {}

  @Get('pdf')
  async downloadCGV(
    @Query('version') version: string,
    @Query('language') language: string,
    @Res() res: Response
  ) {
    const buffer = await this.cgvService.generatePDF(version, language);
    
    res.set({
      'Content-Type': 'application/pdf',
      'Content-Disposition': `attachment; filename=cgv-${version}-${language}.pdf`,
    });
    
    res.send(buffer);
  }

  @Post('send')
  async sendCGV(@Body() body: { email: string; version: string; language?: string }) {
    const buffer = await this.cgvService.generatePDF(body.version, body.language);
    
    await this.emailService.sendEmail({
      to: body.email,
      subject: 'Conditions Générales de Vente',
      text: `Veuillez trouver ci-joint nos CGV version ${body.version}`,
      attachments: [{
        filename: `cgv-${body.version}.pdf`,
        content: buffer
      }]
    });

    return { success: true };
  }
}
