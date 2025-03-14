import { Injectable } from '@nestjs/common';
import { plainAddPlaceholder, SignPdf } from '@signpdf/node';
import * as fs from 'fs/promises';
import * as path from 'path';
import { PDFDocument } from 'pdf-lib';
import { z } from 'zod';

const signConfigSchema = z.object({
  content: z.string(),
  title: z.string(),
  author: z.string(),
  certificatePath: z.string()
});

@Injectable()
export class PdfSignerService {
  private async generatePdf(data: z.infer<typeof signConfigSchema>) {
    const pdfDoc = await PDFDocument.create();
    const page = pdfDoc.addPage();

    // Métadonnées
    pdfDoc.setTitle(data.title);
    pdfDoc.setAuthor(data.author);
    pdfDoc.setCreationDate(new Date());

    const { width, height } = page.getSize();
    page.drawText(data.title, {
      x: 50,
      y: height - 50,
      size: 20
    });

    page.drawText(data.content, {
      x: 50,
      y: height - 100,
      size: 12,
      maxWidth: width - 100
    });

    return Buffer.from(await pdfDoc.save());
  }

  async signPdf(data: z.infer<typeof signConfigSchema>) {
    // Générer le PDF
    let pdfBuffer = await this.generatePdf(data);

    // Ajouter le placeholder pour la signature
    pdfBuffer = plainAddPlaceholder({ pdfBuffer });

    // Lire le certificat
    const p12Buffer = await fs.readFile(data.certificatePath);

    // Signer le PDF
    const signer = new SignPdf();
    return signer.sign(pdfBuffer, p12Buffer);
  }
}
