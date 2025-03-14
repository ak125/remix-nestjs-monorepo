import { Injectable } from "@nestjs/common";
import { PrismaService } from "../prisma/prisma.service";
import { ShippingService } from "../shipping/shipping.service";
import { z } from "zod";

const messageSchema = z.object({
  message: z.string(),
  orderId: z.string().optional(),
  userId: z.string()
});

@Injectable()
export class ChatbotService {
  private readonly responses = {
    tracking: (status: string, date: string) => 
      `📦 Votre colis est ${status}. Livraison prévue le ${date}`,
    delay: "⚠️ Un retard est possible. Notre service client va vous contacter.",
    return: "🔄 Pour effectuer un retour, rendez-vous dans votre espace client.",
    default: "👋 Comment puis-je vous aider avec votre commande ?"
  };

  constructor(
    private prisma: PrismaService,
    private shipping: ShippingService
  ) {}

  async handleMessage(data: unknown) {
    const { message, orderId, userId } = messageSchema.parse(data);

    // Sauvegarder la conversation
    await this.prisma.chatMessage.create({
      data: {
        content: message,
        userId,
        orderId
      }
    });

    if (orderId) {
      const tracking = await this.shipping.getShipmentStatus(orderId);
      if (tracking) {
        return this.responses.tracking(
          tracking.status,
          tracking.estimatedDelivery
        );
      }
    }

    if (message.toLowerCase().includes('retard')) {
      return this.responses.delay;
    }

    if (message.toLowerCase().includes('retour')) {
      return this.responses.return;
    }

    return this.responses.default;
  }

  async getConversationHistory(userId: string) {
    return this.prisma.chatMessage.findMany({
      where: { userId },
      orderBy: { createdAt: 'asc' },
      take: 50
    });
  }
}
