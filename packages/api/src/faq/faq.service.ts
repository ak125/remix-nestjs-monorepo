import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { z } from "zod";

const faqSchema = z.array(z.object({
  id: z.number(),
  question: z.string(),
  answer: z.string(),
  createdAt: z.string().datetime(),
  updatedAt: z.string().datetime()
}));

@Injectable()
export class FaqService {
  constructor(private prisma: PrismaService) {}

  async getFaqs() {
    const faqs = await this.prisma.faq.findMany({
      orderBy: { createdAt: "desc" }
    });

    return faqSchema.parse(faqs);
  }
}
