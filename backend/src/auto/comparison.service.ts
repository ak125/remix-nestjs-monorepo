import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { OpenAI } from 'openai';

@Injectable()
export class ComparisonService {
  private openai: OpenAI;

  constructor(private prisma: PrismaService) {
    this.openai = new OpenAI({ apiKey: process.env.OPENAI_API_KEY });
  }

  async compareModels(carIds: number[], userId: string) {
    // Récupérer les spécifications des modèles
    const specs = await this.prisma.modelSpecification.findMany({
      where: { modelId: { in: carIds } },
      include: { model: true }
    });

    // Générer la comparaison avec GPT-4
    const analysis = await this.generateComparison(specs);

    // Sauvegarder la comparaison
    await this.prisma.carComparison.create({
      data: {
        userId,
        carIds: carIds,
        results: analysis
      }
    });

    // Enregistrer dans l'historique
    await this.recordComparisonHistory(userId, carIds);

    return analysis;
  }

  private async generateComparison(specs: any[]) {
    const response = await this.openai.chat.completions.create({
      model: "gpt-4",
      messages: [
        {
          role: "system",
          content: "Vous êtes un expert automobile. Comparez ces modèles de manière objective."
        },
        {
          role: "user",
          content: JSON.stringify(specs)
        }
      ]
    });

    return response.choices[0].message.content;
  }

  private async recordComparisonHistory(userId: string, carIds: number[]) {
    await Promise.all(carIds.map(modelId => 
      this.prisma.userHistory.create({
        data: {
          userId,
          modelId,
          action: 'compared'
        }
      })
    ));
  }
}
