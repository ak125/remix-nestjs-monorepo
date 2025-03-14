import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { OpenAI } from 'openai';

@Injectable()
export class RecommendationsService {
  private openai: OpenAI;

  constructor(private prisma: PrismaService) {
    this.openai = new OpenAI({ apiKey: process.env.OPENAI_API_KEY });
  }

  async getRecommendations(userId: string) {
    // Récupérer l'historique de l'utilisateur
    const history = await this.prisma.userHistory.findMany({
      where: { userId },
      include: {
        model: {
          include: {
            specifications: true
          }
        }
      },
      orderBy: { createdAt: 'desc' },
      take: 10
    });

    // Générer des recommandations avec GPT-4
    return this.generateRecommendations(history);
  }

  private async generateRecommendations(history: any) {
    const response = await this.openai.chat.completions.create({
      model: "gpt-4",
      messages: [
        {
          role: "system",
          content: "Analysez l'historique d'utilisation et suggérez des modèles similaires."
        },
        {
          role: "user",
          content: JSON.stringify(history)
        }
      ]
    });

    return response.choices[0].message.content;
  }
}
