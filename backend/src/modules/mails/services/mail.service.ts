import { Injectable, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import * as nodemailer from 'nodemailer';

@Injectable()
export class MailService {
  private readonly transporter;
  private readonly logger = new Logger(MailService.name);

  constructor(private readonly config: ConfigService) {
    this.transporter = nodemailer.createTransport({
      service: 'gmail',
      auth: {
        user: this.config.get('EMAIL_USER'),
        pass: this.config.get('EMAIL_PASS'),
      },
    });
  }

  async sendRefundNotification(params: {
    to: string;
    orderId: string;
    amount: number;
    isPartial: boolean;
  }) {
    const { to, orderId, amount, isPartial } = params;

    try {
      await this.transporter.sendMail({
        from: this.config.get('EMAIL_FROM'),
        to,
        subject: isPartial 
          ? `Remboursement partiel de votre commande #${orderId}`
          : `Remboursement de votre commande #${orderId}`,
        html: `
          <h2>Confirmation de remboursement</h2>
          <p>Bonjour,</p>
          <p>Nous vous confirmons le remboursement ${isPartial ? 'partiel ' : ''}
          de votre commande #${orderId} d'un montant de ${amount.toFixed(2)}€.</p>
          <p>Le remboursement sera effectif sous 5-7 jours ouvrés sur votre compte.</p>
        `,
      });

      this.logger.log(`Email de remboursement envoyé pour ${orderId}`);

    } catch (error) {
      this.logger.error('Erreur envoi email remboursement', { error, orderId });
      throw error;
    }
  }
}
