import { Injectable, Logger } from '@nestjs/common';
import * as SendGrid from '@sendgrid/mail';
import { PrismaService } from '../prisma/prisma.service';

@Injectable()
export class EmailService {
  private readonly logger = new Logger(EmailService.name);

  constructor(private prisma: PrismaService) {
    if (!process.env.SENDGRID_API_KEY) {
      throw new Error('SENDGRID_API_KEY is not defined');
    }
    SendGrid.setApiKey(process.env.SENDGRID_API_KEY);
  }

  async sendEmail(params: {
    to: string;
    subject: string;
    html: string;
    templateId?: string;
    dynamicData?: Record<string, any>;
  }) {
    const { to, subject, html, templateId, dynamicData } = params;

    const msg = {
      to,
      from: {
        email: process.env.SENDGRID_FROM_EMAIL,
        name: process.env.SENDGRID_FROM_NAME
      },
      subject,
      html,
      ...(templateId && {
        templateId,
        dynamicTemplateData: dynamicData
      })
    };

    try {
      await SendGrid.send(msg);
      this.logger.log(`Email sent to ${to}`);

      await this.prisma.emailLog.create({
        data: {
          to,
          subject,
          templateId,
          status: 'sent'
        }
      });
    } catch (error) {
      this.logger.error(`Failed to send email to ${to}`, error.stack);
      throw error;
    }
  }

  async sendPriceAlertEmail(alert: {
    userEmail: string;
    modelName: string;
    oldPrice: number;
    newPrice: number;
  }) {
    await this.sendEmail({
      to: alert.userEmail,
      subject: `Baisse de prix sur ${alert.modelName}`,
      templateId: 'price-alert-template',
      dynamicData: {
        modelName: alert.modelName,
        oldPrice: alert.oldPrice,
        newPrice: alert.newPrice,
        saving: alert.oldPrice - alert.newPrice
      }
    });
  }
}
