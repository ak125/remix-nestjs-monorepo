import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { OpenAI } from 'openai';

@Injectable()
export class ChatbotService {
  private openai: OpenAI;

  constructor(private prisma: PrismaService) {
    this.openai = new OpenAI({ apiKey: process.env.OPENAI_API_KEY });
  }

  async getCarAdvice(userInput: string, context?: any) {
    const carSpecs = context?.carSpecs;
    const userHistory = context?.userHistory;

    const response = await this.openai.chat.completions.create({
      model: "gpt-4",
      messages: [
        {
          role: "system",
          content: "Vous êtes un expert automobile spécialisé dans le conseil d'achat."
        },
        {
          role: "user",
          content: `Question: ${userInput}\nContexte voiture: ${JSON.stringify(carSpecs)}\nHistorique: ${JSON.stringify(userHistory)}`
        }
      ]
    });

    await this.saveConversation(userInput, response.choices[0].message.content);
    return response.choices[0].message.content;
  }

  private async saveConversation(question: string, answer: string) {
    // Sauvegarder l'historique des conversations pour améliorer les réponses futures
    await this.prisma.chatMessage.create({
      data: {
        question,
        answer
      }
    });
  }
}
