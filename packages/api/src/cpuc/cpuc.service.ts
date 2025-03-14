import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { PDFDocument, rgb } from 'pdf-lib';
import { z } from 'zod';

const cpucSchema = z.object({
  content: z.string().min(100),
  language: z.string().length(2).default('fr'),
  userId: z.string(),
  userEmail: z.string().email()
});

@Injectable()
export class CPUCService {
  constructor(private prisma: PrismaService) {}

  async getCPUC(language = 'fr') {
    const cpuc = await this.prisma.cPUC.findFirst({
      where: { language },
      orderBy: { version: 'desc' }
    });

    if (!cpuc) {
      throw new NotFoundException('CPUC non trouvées');
    }

    return cpuc;
  }

  async updateCPUC(data: unknown) {
    const validated = cpucSchema.parse(data);

    // Get current version
    const current = await this.prisma.cPUC.findFirst({
      where: { language: validated.language },
      orderBy: { version: 'desc' }
    });

    // Archive current version
    if (current) {
      await this.prisma.cPUCHistory.create({
        data: {
          content: current.content,
          version: current.version,
          language: current.language,
          updatedBy: validated.userId,
          userEmail: validated.userEmail
        }
      });
    }

    // Create new version
    return this.prisma.cPUC.create({
      data: {
        content: validated.content,
        language: validated.language,
        version: (current?.version || 0) + 1,
        updatedBy: validated.userId
      }
    });
  }

  async generatePDF(language = 'fr'): Promise<Buffer> {
    const cpuc = await this.getCPUC(language);
    
    const pdfDoc = await PDFDocument.create();
    const page = pdfDoc.addPage();
    
    const { width, height } = page.getSize();

    // Titre
    page.drawText('Conditions Générales d'Utilisation', {
      x: 50,
      y: height - 50,
      size: 24
    });

    // Version et date
    page.drawText(`Version ${cpuc.version} - ${new Date().toLocaleDateString()}`, {
      x: 50,
      y: height - 80,
      size: 12
    });

    // Contenu
    const lines = this.wrapText(cpuc.content, 80);
    let y = height - 120;

    for (const line of lines) {
      page.drawText(line, {
        x: 50,
        y,
        size: 11,
        color: rgb(0, 0, 0)
      });
      y -= 15;
    }

    return Buffer.from(await pdfDoc.save());
  }

  private wrapText(text: string, maxWidth: number): string[] {
    const words = text.split(' ');
    const lines: string[] = [];
    let currentLine = words[0];

    for (let i = 1; i < words.length; i++) {
      if ((currentLine + ' ' + words[i]).length <= maxWidth) {
        currentLine += ' ' + words[i];
      } else {
        lines.push(currentLine);
        currentLine = words[i];
      }
    }
    lines.push(currentLine);

    return lines;
  }
}
