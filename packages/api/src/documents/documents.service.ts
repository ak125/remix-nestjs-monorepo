import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import * as ExcelJS from "exceljs";
import * as puppeteer from "puppeteer";
import { z } from "zod";
import * as path from "path";
import * as fs from "fs";

@Injectable()
export class DocumentsService {
  constructor(private prisma: PrismaService) {}

  async generateInvoicePDF(payment: any) {
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
          </style>
        </head>
        <body>
          <div class="invoice">
            <div class="header">
              <h1>Facture</h1>
              <p>Référence: ${payment.id}</p>
              <p>Date: ${new Date().toLocaleDateString()}</p>
            </div>
            <div class="details">
              <p>Client: ${payment.customer_email}</p>
              <p>Montant: <span class="amount">${(payment.amount / 100).toFixed(2)}€</span></p>
              <p>Statut: ${payment.status}</p>
            </div>
          </div>
        </body>
      </html>
    `;

    await page.setContent(template);
    const pdfPath = path.join(process.cwd(), "tmp", `invoice-${payment.id}.pdf`);

    await page.pdf({
      path: pdfPath,
      format: "A4",
      margin: { top: "20px", right: "20px", bottom: "20px", left: "20px" }
    });

    await browser.close();
    return pdfPath;
  }

  async generatePaymentsExcel(payments: any[]) {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Paiements");

    worksheet.columns = [
      { header: "ID", key: "id", width: 30 },
      { header: "Date", key: "date", width: 15 },
      { header: "Email", key: "email", width: 30 },
      { header: "Montant", key: "amount", width: 15 },
      { header: "Statut", key: "status", width: 15 }
    ];

    payments.forEach(p => {
      worksheet.addRow({
        id: p.id,
        date: new Date(p.created * 1000).toLocaleDateString(),
        email: p.customer_email || "N/A",
        amount: `${(p.amount / 100).toFixed(2)}€`,
        status: p.status
      });
    });

    const filePath = path.join(process.cwd(), "tmp", "payments.xlsx");
    await workbook.xlsx.writeFile(filePath);
    return filePath;
  }
}
