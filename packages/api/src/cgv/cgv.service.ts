import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { PDFDocument, rgb } from 'pdf-lib';
import { createHash } from 'crypto';
import { z } from 'zod';

const cgvSchema = z.object({
  content: z.string().min(100),
  version: z.string().regex(/^\d+\.\d+$/),
  language: z.string().length(2).default('fr'),
  userId: z.string(),
  userEmail: z.string().email()
});

@Injectable()
export class CGVService {
  constructor(
    private prisma: PrismaService,
    private docusign: DocuSignService
  ) {}

  async generatePDF(version: string, language = 'fr'): Promise<Buffer> {
    const cgv = await this.prisma.cGV.findFirst({
      where: { version, language }
    });

    if (!cgv) {
      throw new NotFoundException(`CGV version ${version} not found`);
    }

    const pdfDoc = await PDFDocument.create();
    const page = pdfDoc.addPage();
    
    const { width, height } = page.getSize();
    
    // En-tête
    page.drawText('Conditions Générales de Vente', {
      x: 50,
      y: height - 50,
      size: 24
    });

    // Version et date
    page.drawText(`Version ${version} - ${new Date().toLocaleDateString()}`, {
      x: 50,
      y: height - 80,
      size: 12
    });

    // Contenu
    const lines = this.wrapText(cgv.content, 80);
    let y = height - 120;

    for (const line of lines) {
      if (y < 50) {
        y = height - 50;
        page = pdfDoc.addPage();
      }
      
      page.drawText(line, {
        x: 50,
        y,
        size: 11
      });
      y -= 15;
    }

    // Signature
    const signature = this.generateSignature(cgv);
    page.drawText(`Signature: ${signature}`, {
      x: 50,
      y: 50,
      size: 8,
      color: rgb(0.5, 0.5, 0.5)
    });

    return Buffer.from(await pdfDoc.save());
  }

  private generateSignature(cgv: any): string {
    const data = `${cgv.version}|${cgv.content}|${new Date().toISOString()}`;
    return createHash('sha256').update(data).digest('hex');
  }

  private wrapText(text: string, maxWidth: number): string[] {
    return text.split('\n').map(paragraph => 
      paragraph.match(new RegExp(`.{1,${maxWidth}}(?:\\s|$)`, 'g')) || []
    ).flat();
  }

  async archiveVersion(data: unknown) {
    const validated = cgvSchema.parse(data);

    return this.prisma.cGVArchive.create({
      data: {
        ...validated,
        signature: this.generateSignature(validated)
      }
    });
  }

  async requestSignature(data: unknown) {
    const envelope = await this.docusign.sendForSignature(data);
    
    return this.prisma.signature.create({
      data: {
        ...data,
        envelopeId: envelope.envelopeId,
        status: 'pending'
      }
    });
  }

  async getSignatureHistory(filters?: {
    version?: string;
    email?: string;
    status?: string;
  }) {
    return this.prisma.signature.findMany({
      where: filters,
      orderBy: { createdAt: 'desc' },
      include: {
        user: {
          select: {
            name: true,
            email: true
          }
        }
      }
    });
  }
}
