import { Injectable, NotFoundException, UnauthorizedException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { PDFDocument } from 'pdf-lib';
import { z } from 'zod';
import { PdfSignerService } from './pdf-signer.service';
import { EmailService } from '../email/email.service';

const policySchema = z.object({
  content: z.string().min(10),
  language: z.string().length(2),
  userId: z.string(),
  userEmail: z.string().email()
});

@Injectable()
export class SecurityService {
  constructor(
    private prisma: PrismaService,
    private pdfSigner: PdfSignerService,
    private email: EmailService
  ) {}

  async getSecurityPolicy(language: string) {
    const policy = await this.prisma.securityPolicy.findFirst({
      where: { language },
      orderBy: { version: 'desc' }
    });

    if (!policy) {
      throw new NotFoundException(`Policy not found for language: ${language}`);
    }

    return policy;
  }

  async updateSecurityPolicy(data: unknown) {
    const { content, language, userId, userEmail } = policySchema.parse(data);

    // Archiver l'ancienne version
    const current = await this.prisma.securityPolicy.findFirst({
      where: { language },
      orderBy: { version: 'desc' }
    });

    if (current) {
      await this.prisma.securityArchive.create({
        data: {
          content: current.content,
          language,
          version: current.version,
          archivedBy: userId
        }
      });
    }

    // Créer la nouvelle version
    const newVersion = (current?.version || 0) + 1;
    const updated = await this.prisma.securityPolicy.create({
      data: {
        content,
        language,
        version: newVersion
      }
    });

    // Notifier les administrateurs
    await this.email.sendTemplated({
      template: 'security-update',
      to: process.env.ADMIN_EMAILS!.split(','),
      subject: `Politique de sécurité mise à jour (v${newVersion})`,
      context: {
        version: newVersion,
        language,
        content,
        updatedBy: userEmail
      }
    });

    return updated;
  }

  async getPolicyHistory(language: string) {
    return this.prisma.securityLog.findMany({
      where: { language },
      orderBy: { createdAt: 'desc' },
      take: 50
    });
  }

  async generatePolicyPDF(language: string): Promise<Buffer> {
    const policy = await this.getSecurityPolicy(language);
    
    const pdfDoc = await PDFDocument.create();
    const page = pdfDoc.addPage();
    
    const { width, height } = page.getSize();
    page.drawText('Politique de Sécurité', {
      x: 50,
      y: height - 50,
      size: 20
    });
    
    page.drawText(policy.content, {
      x: 50,
      y: height - 100,
      size: 12,
      maxWidth: width - 100
    });

    return Buffer.from(await pdfDoc.save());
  }

  async generateSignedPdf(language: string) {
    const policy = await this.getSecurityPolicy(language);

    return this.pdfSigner.signPdf({
      content: policy.content,
      title: `Politique de Sécurité - ${language.toUpperCase()}`,
      author: 'System Admin',
      certificatePath: process.env.SECURITY_CERT_PATH!
    });
  }

  async getUserSessions(userId: string) {
    return this.prisma.session.findMany({
      where: { userId },
      orderBy: { createdAt: 'desc' },
      take: 10
    });
  }

  async revokeSession(sessionId: string, userId: string) {
    const session = await this.prisma.session.findUnique({
      where: { id: sessionId }
    });

    if (!session || session.userId !== userId) {
      throw new UnauthorizedException();
    }

    return this.prisma.session.delete({
      where: { id: sessionId }
    });
  }
}
