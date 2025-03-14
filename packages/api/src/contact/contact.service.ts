import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { z } from "zod";
import * as nodemailer from "nodemailer";

const contactSchema = z.object({
  name: z.string().min(2, "Nom trop court"),
  email: z.string().email("Email invalide"),
  message: z.string().min(10, "Message trop court")
});

@Injectable()
export class ContactService {
  private transporter;

  constructor(private prisma: PrismaService) {
    this.transporter = nodemailer.createTransport({
      service: "gmail",
      auth: {
        user: process.env.EMAIL_USER,
        pass: process.env.EMAIL_PASS
      }
    });
  }

  async submitContactForm(data: unknown) {
    const validatedData = contactSchema.parse(data);

    const message = await this.prisma.contactMessage.create({
      data: validatedData
    });

    await this.transporter.sendMail({
      from: process.env.EMAIL_USER,
      to: validatedData.email,
      subject: "Confirmation de votre message",
      text: `Merci ${validatedData.name}, nous avons bien reçu votre message.`
    });

    return message;
  }
}
