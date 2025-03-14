import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { OpenAI } from 'openai';

@Injectable()
export class ReviewsService {
  private openai: OpenAI;

  constructor(private prisma: PrismaService) {
    this.openai = new OpenAI({ apiKey: process.env.OPENAI_API_KEY });
  }

  async getModelReviews(modelId: number) {
    return this.prisma.review.findMany({
      where: { modelId },
      include: {
        user: {
          select: {
            name: true
          }
        }
      }
    });
  }

  async createReview(data: {
    modelId: number;
    userId: string;
    rating: number;
    comment: string;
  }) {
    return this.prisma.review.create({
      data
    });
  }

  async generateAIInsights(modelId: number) {
    const [reviews, specs] = await Promise.all([
      this.getModelReviews(modelId),
      this.prisma.modelSpecification.findUnique({
        where: { modelId }
      })
    ]);

    const response = await this.openai.chat.completions.create({
      model: "gpt-4",
      messages: [
        {
          role: "system",
          content: "Analyse les avis clients et les spécifications pour générer un rapport détaillé."
        },
        {
          role: "user", 
          content: JSON.stringify({ reviews, specs })
        }
      ]
    });

    return response.choices[0].message.content;
  }
}
