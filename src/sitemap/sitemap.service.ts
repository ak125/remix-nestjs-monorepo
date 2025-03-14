import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { RedisService } from '../redis/redis.service';
import axios from 'axios';
import * as fs from 'fs';
import * as path from 'path';
import * as nodemailer from 'nodemailer';

@Injectable()
export class SitemapService {
  private logFilePath = path.join(__dirname, '../../logs/sitemap.log');
  private adminEmails = ['admin@monsite.com', 'support@monsite.com'];
  private maxFailuresBeforeAlert = 3; // Nombre d'échecs avant alerte

  constructor(
    private readonly prisma: PrismaService,
    private readonly redisService: RedisService,
  ) {}

  async generateSitemap() {
    const domain = 'https://mon-site.com';

    let pages = [
      { loc: '/', priority: 1.0, changefreq: 'daily' },
      { loc: '/blog', priority: 0.8, changefreq: 'weekly' },
      { loc: '/contact', priority: 0.6, changefreq: 'monthly' },
    ];

    const articles = await this.prisma.article.findMany({
      select: { slug: true }
    });

    articles.forEach(article => {
      pages.push({
        loc: `/blog/${article.slug}`,
        priority: 0.7,
        changefreq: 'weekly',
      });
    });

    let xml = `<?xml version="1.0" encoding="UTF-8"?>\n`;
    xml += `<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n`;

    pages.forEach(page => {
      xml += `  <url>\n`;
      xml += `    <loc>${domain}${page.loc}</loc>\n`;
      xml += `    <changefreq>${page.changefreq}</changefreq>\n`;
      xml += `    <priority>${page.priority}</priority>\n`;
      xml += `  </url>\n`;
    });

    xml += `</urlset>`;

    await this.redisService.set('sitemap', xml, 3600);

    // Notifier tous les moteurs de recherche
    await this.notifySearchEngines();

    return xml;
  }

  async notifySearchEngines() {
    const sitemapUrl = "https://mon-site.com/sitemap.xml";

    const engines = {
      Google: `https://www.google.com/ping?sitemap=${encodeURIComponent(sitemapUrl)}`,
      Bing: `https://www.bing.com/ping?sitemap=${encodeURIComponent(sitemapUrl)}`,
      Yahoo: `https://search.yahoo.com/ping?sitemap=${encodeURIComponent(sitemapUrl)}`,
      Yandex: `https://yandex.com/indexnow?url=${encodeURIComponent(sitemapUrl)}`,
      DuckDuckGo: `https://duckduckgo.com/?q=${encodeURIComponent(sitemapUrl)}`,
      Ask: `https://www.ask.com/ping?sitemap=${encodeURIComponent(sitemapUrl)}`,
    };

    for (const [engine, url] of Object.entries(engines)) {
      try {
        const response = await axios.get(url);
        const logMessage = `✅ ${engine} notifié avec succès ! Status: ${response.status}`;
        
        // Réinitialiser les erreurs en base de données
        await this.prisma.notificationError.deleteMany({ where: { engine } });

        await this.prisma.sitemapNotificationLog.create({
          data: {
            engine,
            statusCode: response.status,
            message: 'Success',
          }
        });

        this.logToFile(logMessage);
        console.log(logMessage);

      } catch (error) {
        const statusCode = error.response?.status || 500;
        const logMessage = `❌ Erreur lors de la notification à ${engine}: ${error.message}`;

        // Vérifier si cette erreur est récurrente
        const existingError = await this.prisma.notificationError.findFirst({
          where: { engine },
        });

        if (existingError) {
          // Incrémenter le nombre d'échecs
          const newFailureCount = existingError.failureCount + 1;
          await this.prisma.notificationError.update({
            where: { id: existingError.id },
            data: { failureCount: newFailureCount, lastAttempt: new Date() },
          });

          if (newFailureCount >= this.maxFailuresBeforeAlert) {
            // Notifier les admins par email
            await this.sendAdminAlert(engine, logMessage);
          }
        } else {
          // Créer une entrée pour suivre les erreurs
          await this.prisma.notificationError.create({
            data: { engine, failureCount: 1 },
          });
        }

        await this.prisma.sitemapNotificationLog.create({
          data: {
            engine,
            statusCode,
            message: error.message,
          }
        });

        this.logToFile(logMessage);
        console.error(logMessage);
      }
    }
  }

  private async sendAdminAlert(engine: string, errorMessage: string) {
    const transporter = nodemailer.createTransport({
      host: process.env.SMTP_HOST || "smtp.mailtrap.io", 
      port: parseInt(process.env.SMTP_PORT || "2525"),
      auth: {
        user: process.env.SMTP_USER || "YOUR_SMTP_USER",
        pass: process.env.SMTP_PASS || "YOUR_SMTP_PASS"
      }
    });

    const mailOptions = {
      from: '"Sitemap Bot" <noreply@monsite.com>',
      to: this.adminEmails.join(','),
      subject: `🚨 Erreur récurrente: Problème avec ${engine}`,
      text: `Une erreur répétée a été détectée avec ${engine} lors de l'envoi du sitemap.\n\nDétails:\n${errorMessage}\n\nVeuillez vérifier.`,
      html: `
        <h2>🚨 Erreur récurrente détectée</h2>
        <p>Une erreur répétée a été détectée avec <strong>${engine}</strong> lors de l'envoi du sitemap.</p>
        <h3>Détails:</h3>
        <pre>${errorMessage}</pre>
        <p>Veuillez vérifier la configuration ou contacter le service concerné.</p>
      `
    };

    try {
      await transporter.sendMail(mailOptions);
      this.logToFile(`📧 Email d'alerte envoyé aux administrateurs pour ${engine}`);
      console.log(`📧 Email d'alerte envoyé aux administrateurs pour ${engine}`);
    } catch (emailError) {
      const emailErrorMsg = `❌ Erreur lors de l'envoi de l'email d'alerte: ${emailError.message}`;
      this.logToFile(emailErrorMsg);
      console.error(emailErrorMsg);
    }
  }

  private logToFile(message: string) {
    const logMessage = `${new Date().toISOString()} - ${message}\n`;
    fs.appendFileSync(this.logFilePath, logMessage, 'utf8');
  }
}
