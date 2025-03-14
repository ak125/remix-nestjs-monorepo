import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import * as webpush from 'web-push';

@Injectable()
export class NotificationsService {
  constructor(private prisma: PrismaService) {
    webpush.setVapidDetails(
      'mailto:support@example.com',
      process.env.VAPID_PUBLIC_KEY,
      process.env.VAPID_PRIVATE_KEY
    );
  }

  async createNotification(data: {
    userId: string;
    modelId?: number;
    type: string;
    message: string;
  }) {
    const notification = await this.prisma.notification.create({
      data: {
        userId: data.userId,
        modelId: data.modelId,
        type: data.type,
        message: data.message
      }
    });

    await this.sendPushNotification(data.userId, {
      title: 'Nouvelle notification',
      body: data.message
    });

    return notification;
  }

  async sendPushNotification(userId: string, payload: { title: string; body: string }) {
    const subscription = await this.prisma.pushSubscription.findUnique({
      where: { userId }
    });

    if (!subscription) return;

    try {
      await webpush.sendNotification(
        {
          endpoint: subscription.endpoint,
          keys: {
            auth: subscription.auth,
            p256dh: subscription.p256dh
          }
        },
        JSON.stringify(payload)
      );
    } catch (error) {
      console.error('Push notification failed:', error);
    }
  }
}
