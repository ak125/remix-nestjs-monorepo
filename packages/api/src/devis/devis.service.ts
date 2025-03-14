import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { PDFDocument, rgb } from 'pdf-lib';
import { z } from 'zod';

const devisSchema = z.object({
  clientName: z.string().min(2),
  amount: z.number().positive(),
  details: z.string().min(10),
  email: z.string().email(),
  deviceInfo: z.object({
    type: z.string(),
    os: z.string(),
    browser: z.string()
  }).optional()
});

@Injectable()
export class DevisService {
  constructor(private prisma: PrismaService) {}

  async createDevis(data: unknown) {
    const validated = devisSchema.parse(data);

    let device = null;
    if (validated.deviceInfo) {
      device = await this.prisma.device.create({
        data: validated.deviceInfo
      });
    }

    return this.prisma.devisRequest.create({
      data: {
        name: validated.clientName,
        email: validated.email,
        details: validated.details,
        deviceId: device?.id
      },
      include: {
        device: true
      }
    });
  }

  async getDevis(id: string) {
    const devis = await this.prisma.devisRequest.findUnique({
      where: { id },
      include: {
        device: true
      }
    });

    if (!devis) {
      throw new NotFoundException(`Devis ${id} non trouvé`);
    }

    return devis;
  }

  async generatePDF(id: string): Promise<Buffer> {
    const devis = await this.getDevis(id);
    
    const pdfDoc = await PDFDocument.create();
    const page = pdfDoc.addPage();

    const { width, height } = page.getSize();
    
    // En-tête
    page.drawText('DEVIS', {
      x: 50,
      y: height - 50,
      size: 24
    });

    // Infos client
    page.drawText(`Client: ${devis.name}`, {
      x: 50,
      y: height - 100,
      size: 12
    });

    page.drawText(`Email: ${devis.email}`, {
      x: 50,
      y: height - 120,
      size: 12
    });

    // Détails appareil
    if (devis.device) {
      page.drawText('Informations Appareil:', {
        x: 50,
        y: height - 160,
        size: 14
      });

      page.drawText(`Type: ${devis.device.type}`, {
        x: 70,
        y: height - 180,
        size: 12
      });

      page.drawText(`OS: ${devis.device.os}`, {
        x: 70,
        y: height - 200,
        size: 12
      });
    }

    // Détails demande
    page.drawText('Détails de la demande:', {
      x: 50,
      y: height - 240,
      size: 14
    });

    const detailsLines = devis.details.split('\n');
    let yPosition = height - 260;
    
    for (const line of detailsLines) {
      page.drawText(line, {
        x: 70,
        y: yPosition,
        size: 12
      });
      yPosition -= 20;
    }

    return Buffer.from(await pdfDoc.save());
  }

  async getStats() {
    const [total, byDevice] = await Promise.all([
      this.prisma.devisRequest.count(),
      this.prisma.device.groupBy({
        by: ['type'],
        _count: true
      })
    ]);

    return {
      total,
      byDevice: byDevice.reduce((acc, curr) => {
        acc[curr.type] = curr._count;
        return acc;
      }, {} as Record<string, number>)
    };
  }
}
