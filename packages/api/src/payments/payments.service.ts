import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { DocumentsService } from "../documents/documents.service";
import * as Stripe from "stripe";
import * as nodemailer from "nodemailer";
import * as PDFDocument from "pdfkit";
import * as fs from "fs";
import { z } from "zod";

const dateRangeSchema = z.object({
  startDate: z.string(),
  endDate: z.string()
});

@Injectable()
export class PaymentsService {
  private stripe: Stripe;
  private transporter;

  constructor(
    private prisma: PrismaService,
    private documents: DocumentsService
  ) {
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

  async handlePaymentSuccess(session: Stripe.Checkout.Session) {
    const devis = await this.prisma.devisRequest.findUnique({
      where: { id: session.client_reference_id! }
    });

    if (!devis) {
      throw new Error("Devis non trouvé");
    }

    // Générer le PDF
    const filePath = `/tmp/devis-${devis.id}.pdf`;
    const doc = new PDFDocument();
    doc.pipe(fs.createWriteStream(filePath));

    // Contenu du PDF
    doc.fontSize(20).text("Devis - Payé", { align: "center" }).moveDown();
    doc.fontSize(14);
    doc.text(`Référence: ${devis.id}`);
    doc.text(`Client: ${devis.name}`);
    doc.text(`Email: ${devis.email}`);
    doc.text(`Montant: ${(session.amount_total! / 100).toFixed(2)}€`);
    doc.text(`Date: ${new Date().toLocaleDateString()}`);
    doc.moveDown();
    doc.text("Détails:");
    doc.text(devis.details);
    
    doc.end();

    // Envoyer l'email avec le PDF
    await this.transporter.sendMail({
      from: process.env.SMTP_FROM,
      to: devis.email,
      subject: "Votre devis - Paiement confirmé",
      html: `
        <h1>Merci pour votre paiement</h1>
        <p>Vous trouverez votre devis en pièce jointe.</p>
      `,
      attachments: [{
        filename: "devis.pdf",
        path: filePath
      }]
    });

    // Nettoyer le fichier temporaire
    fs.unlinkSync(filePath);
  }

  async getPaymentStats() {
    const [payments, totalAmount] = await Promise.all([
      this.stripe.charges.list({ limit: 100 }),
      this.stripe.charges.list().autoPagingToArray({ limit: 10000 })
        .then(charges => charges.reduce((sum, charge) => sum + charge.amount, 0))
    ]);

    return {
      recentPayments: payments.data.map(p => ({
        id: p.id,
        amount: p.amount / 100,
        date: new Date(p.created * 1000).toISOString(),
        status: p.status
      })),
      stats: {
        totalAmount: totalAmount / 100,
        count: payments.data.length
      }
    };
  }

  async getPaymentsByDate(startDate: string, endDate: string) {
    const { startDate: start, endDate: end } = dateRangeSchema.parse({
      startDate,
      endDate
    });

    const startTimestamp = new Date(start).getTime() / 1000;
    const endTimestamp = new Date(end).getTime() / 1000;

    const payments = await this.stripe.paymentIntents.list({
      created: {
        gte: startTimestamp,
        lte: endTimestamp
      },
      limit: 100
    });

    return payments.data.map(p => ({
      id: p.id,
      amount: p.amount / 100,
      currency: p.currency.toUpperCase(),
      status: p.status,
      email: p.receipt_email,
      date: new Date(p.created * 1000).toISOString()
    }));
  }

  async refundPayment(paymentId: string) {
    try {
      const refund = await this.stripe.refunds.create({
        payment_intent: paymentId
      });

      await this.prisma.payment.update({
        where: { stripeId: paymentId },
        data: { status: "refunded", refundId: refund.id }
      });

      return { success: true, refundId: refund.id };
    } catch (error) {
      return { success: false, message: error.message };
    }
  }

  async getClientPayments(email: string) {
    return this.prisma.payment.findMany({
      where: { email },
      orderBy: { createdAt: "desc" },
      include: {
        devis: true
      }
    });
  }

  async downloadInvoice(paymentId: string) {
    const payment = await this.stripe.paymentIntents.retrieve(paymentId);
    if (!payment) {
      throw new Error("Paiement non trouvé");
    }

    return this.documents.generateInvoicePDF(payment);
  }

  async downloadPaymentsExcel() {
    const payments = await this.stripe.paymentIntents.list({ limit: 100 });
    return this.documents.generatePaymentsExcel(payments.data);
  }
}
