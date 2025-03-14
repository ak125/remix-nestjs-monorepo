import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { WebSocketGateway, WebSocketServer } from '@nestjs/websockets';
import { Server } from 'socket.io';
import { z } from 'zod';

const logSchema = z.object({
  type: z.string(),
  details: z.any(),
  userId: z.string().optional(),
  ipAddress: z.string().optional()
});

const notificationSchema = z.object({
  userId: z.string(),
  title: z.string(),
  message: z.string(),
  type: z.string()
});

@WebSocketGateway({
  cors: {
    origin: process.env.CLIENT_URL
  }
})
@Injectable()
export class NotificationService {
  @WebSocketServer()
  server: Server;

  constructor(private prisma: PrismaService) {}

  async logAction(data: unknown) {
    const validated = logSchema.parse(data);

    const log = await this.prisma.actionLog.create({
      data: validated
    });

    // Emit log event for real-time monitoring
    this.server.emit('newLog', log);

    return log;
  }

  async createNotification(data: unknown) {
    const validated = notificationSchema.parse(data);

    const notification = await this.prisma.userNotification.create({
      data: validated,
      include: {
        user: {
          select: {
            email: true
          }
        }
      }
    });

    // Emit notification to specific user
    this.server.to(validated.userId).emit('notification', notification);

    return notification;
  }

  async markAsRead(userId: string, notificationId: string) {
    return this.prisma.userNotification.update({
      where: {
        id: notificationId,
        userId
      },
      data: {
        isRead: true,
        readAt: new Date()
      }
    });
  }
}
