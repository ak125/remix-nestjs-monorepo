import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import * as Stripe from "stripe";
import * as twilio from "twilio";
import * as nodemailer from "nodemailer";
import { z } from "zod";

const notificationSchema = z.object({
  phoneNumber: z.string(),
  amount: z.number(),
  type: z.enum(['payment_failed', 'subscription_ending', 'refund_processed'])
});

@Injectable()
export class NotificationsService {
  private stripe: Stripe;
  private twilio: twilio.Twilio;
  private mailer: nodemailer.Transporter;

  constructor(private prisma: PrismaService) {
    this.stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
      apiVersion: "2023-10-16"
    });

    this.twilio = twilio(
      process.env.TWILIO_ACCOUNT_SID!,
      process.env.TWILIO_AUTH_TOKEN!
    );

    this.mailer = nodemailer.createTransport({
      host: process.env.SMTP_HOST,
      port: Number(process.env.SMTP_PORT),
      secure: true,
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS
      }
    });
  }

  async sendPaymentReminder(data: unknown) {
    const validated = notificationSchema.parse(data);
    
    const templates = {
      payment_failed: `Votre paiement de ${validated.amount}€ a échoué. Cliquez ici pour le régulariser: ${process.env.FRONTEND_URL}/payment/retry`,
      subscription_ending: `Votre abonnement expire bientôt. Renouvelez-le maintenant et bénéficiez de -10%: ${process.env.FRONTEND_URL}/subscription`,
      refund_processed: `Votre remboursement de ${validated.amount}€ a été traité. Il sera visible sur votre compte sous 5-7 jours.`
    };

    await this.twilio.messages.create({
      body: templates[validated.type],
      from: process.env.TWILIO_PHONE_NUMBER,
      to: validated.phoneNumber
    });

    await this.prisma.notification.create({
      data: {
        type: validated.type,
        phoneNumber: validated.phoneNumber,
        amount: validated.amount,
        status: 'sent'
      }
    });
  }

  async getNotificationHistory(userId: string) {
    return this.prisma.notification.findMany({
      where: { userId },
      orderBy: { createdAt: 'desc' },
      take: 50
    });
  }

  async checkExpiringSubscriptions() {
    const subscriptions = await this.stripe.subscriptions.list({
      status: 'active',
      expand: ['data.customer']
    });

    for (const subscription of subscriptions.data) {
      const daysUntilExpiry = this.getDaysUntilExpiry(subscription.current_period_end);
      
      if ([7, 3, 0].includes(daysUntilExpiry)) {
        await this.sendExpiryNotifications(subscription, daysUntilExpiry);
      }
    }
  }

  private async sendExpiryNotifications(subscription: Stripe.Subscription, daysLeft: number) {
    const customer = subscription.customer as Stripe.Customer;
    const renewalUrl = `${process.env.FRONTEND_URL}/subscription/renew/${subscription.id}`;

    // Email notification
    await this.mailer.sendMail({
      from: process.env.SMTP_FROM,
      to: customer.email,
      subject: `Votre abonnement expire dans ${daysLeft} jours`,
      html: `
        <h1>Rappel d'expiration d'abonnement</h1>
        <p>Votre abonnement expire ${daysLeft === 0 ? "aujourd'hui" : `dans ${daysLeft} jours`}.</p>
        <p><a href="${renewalUrl}">Cliquez ici pour renouveler votre abonnement</a></p>
      `
    });

    // SMS notification if phone number exists
    if (customer.phone) {
      await this.twilio.messages.create({
        body: `Votre abonnement expire ${daysLeft === 0 ? "aujourd'hui" : `dans ${daysLeft} jours`}. Renouvelez-le ici: ${renewalUrl}`,
        from: process.env.TWILIO_PHONE_NUMBER,
        to: customer.phone
      });
    }

    // Log notification
    await this.prisma.notification.create({
      data: {
        type: 'subscription_expiry',
        customerId: customer.id,
        message: `Notification d'expiration envoyée (J-${daysLeft})`,
        channels: customer.phone ? ['email', 'sms'] : ['email']
      }
    });
  }

  private getDaysUntilExpiry(timestamp: number): number {
    const now = new Date();
    const expiryDate = new Date(timestamp * 1000);
    const diffTime = expiryDate.getTime() - now.getTime();
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  }

  private async makeRequest(url: string, options: RequestInit = {}) {
    const response = await fetch(url, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        ...options.headers
      }
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  async sendWebhookNotification(data: unknown) {
    return this.makeRequest(process.env.WEBHOOK_URL!, {
      method: 'POST',
      body: JSON.stringify(data)
    });
  }
}
