import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import * as Stripe from "stripe";
import * as nodemailer from "nodemailer";
import * as puppeteer from "puppeteer";
import { z } from "zod";

@Injectable()
export class BillingService {
  private stripe: Stripe;
  private transporter;

  constructor(private prisma: PrismaService) {
    this.stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
      apiVersion: "2023-10-16"
    });

    this.transporter = nodemailer.createTransport({
      host: process.env.SMTP_HOST,
      port: Number(process.env.SMTP_PORT),
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS
      }
    });
  }

  async createSubscription(customerId: string, priceId: string) {
    const subscription = await this.stripe.subscriptions.create({
      customer: customerId,
      items: [{ price: priceId }],
      payment_behavior: 'default_incomplete',
      expand: ['latest_invoice.payment_intent']
    });

    await this.prisma.subscription.create({
      data: {
        stripeId: subscription.id,
        customerId,
        status: subscription.status,
        priceId
      }
    });

    return subscription;
  }

  async createPaymentPlan(customerId: string, amount: number, installments: number) {
    // Créer le plan de paiement
    const schedule = await this.stripe.subscriptionSchedules.create({
      customer: customerId,
      start_date: 'now',
      end_behavior: 'cancel',
      phases: [{
        items: [{
          price_data: {
            currency: 'eur',
            product: 'prod_payment_plan',
            recurring: {
              interval: 'month',
              interval_count: 1
            },
            unit_amount: Math.round(amount * 100 / installments)
          },
          quantity: 1
        }],
        iterations: installments
      }]
    });

    // Enregistrer le plan
    await this.prisma.paymentPlan.create({
      data: {
        stripeId: schedule.id,
        customerId,
        amount,
        installments,
        status: schedule.status
      }
    });

    return schedule;
  }

  async cancelSubscription(subscriptionId: string) {
    const subscription = await this.stripe.subscriptions.cancel(subscriptionId);

    await this.prisma.subscription.update({
      where: { stripeId: subscriptionId },
      data: { status: subscription.status }
    });

    return subscription;
  }

  async getCustomerHistory(customerId: string) {
    const [payments, subscription] = await Promise.all([
      this.stripe.charges.list({ customer: customerId }),
      this.prisma.subscription.findFirst({
        where: { customerId, status: 'active' }
      })
    ]);

    return {
      payments: payments.data.map(p => ({
        id: p.id,
        amount: p.amount / 100,
        created: new Date(p.created * 1000).toISOString(),
        status: p.status
      })),
      subscription
    };
  }

  async handlePaymentSuccess(session: Stripe.Checkout.Session) {
    const invoice = await this.generateInvoicePDF({
      id: session.id,
      amount: session.amount_total!,
      customer_email: session.customer_details?.email!,
      subscription_id: session.subscription
    });

    await this.sendInvoiceEmail(
      session.customer_details?.email!,
      invoice.path,
      session.amount_total! / 100
    );

    return invoice;
  }

  private async generateInvoicePDF(data: any) {
    const browser = await puppeteer.launch({ headless: "new" });
    const page = await browser.newPage();

    const template = `
      <!DOCTYPE html>
      <html>
        <head>
          <style>
            body { font-family: system-ui, sans-serif; padding: 40px; }
            .invoice { max-width: 800px; margin: 0 auto; }
            .header { border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; }
            .details { margin: 20px 0; }
            .amount { font-size: 24px; color: #1e40af; }
            .subscription { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
          </style>
        </head>
        <body>
          <div class="invoice">
            <div class="header">
              <h1>Facture</h1>
              <p>Référence: ${data.id}</p>
              <p>Date: ${new Date().toLocaleDateString()}</p>
            </div>
            <div class="details">
              <p>Email: ${data.customer_email}</p>
              <p>Montant: <span class="amount">${(data.amount / 100).toFixed(2)}€</span></p>
              ${data.subscription_id ? `
                <div class="subscription">
                  <p>Abonnement: ${data.subscription_id}</p>
                </div>
              ` : ''}
            </div>
          </div>
        </body>
      </html>
    `;

    await page.setContent(template);
    const path = `/tmp/invoice-${data.id}.pdf`;
    
    await page.pdf({
      path,
      format: 'A4',
      margin: { top: '20px', right: '20px', bottom: '20px', left: '20px' }
    });

    await browser.close();
    return { path };
  }

  private async sendInvoiceEmail(email: string, pdfPath: string, amount: number) {
    await this.transporter.sendMail({
      from: process.env.SMTP_FROM,
      to: email,
      subject: "Votre facture",
      html: `
        <h1>Merci pour votre paiement</h1>
        <p>Vous trouverez ci-joint votre facture d'un montant de ${amount}€.</p>
      `,
      attachments: [{
        filename: 'facture.pdf',
        path: pdfPath
      }]
    });
  }
}
