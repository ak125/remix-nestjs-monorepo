import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { AdminNotificationGateway } from '../gateways/notification.gateway';

interface CreateNotificationDto {
  type: string;
  category: string;
  title: string;
  message: string;
  priority?: 'LOW' | 'NORMAL' | 'HIGH' | 'URGENT';
  metadata?: Record<string, any>;
}

@Injectable()
export class AdminNotificationsService {
  private readonly logger = new Logger(AdminNotificationsService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly notificationGateway: AdminNotificationGateway,
  ) {}

  async getNotifications(params: {
    isRead?: boolean;
    type?: string;
    category?: string;
    priority?: string;
    page?: number;
    limit?: number;
  }) {
    const { isRead, type, category, priority, page = 1, limit = 20 } = params;

    const [notifications, total] = await Promise.all([
      this.prisma.adminNotification.findMany({
        where: {
          isRead: typeof isRead === 'boolean' ? isRead : undefined,
          type: type || undefined,
          category: category || undefined,
          priority: priority || undefined,
        },
        orderBy: [
          { priority: 'desc' },
          { createdAt: 'desc' },
        ],
        take: limit,
        skip: (page - 1) * limit,
      }),
      this.prisma.adminNotification.count({
        where: {
          isRead: typeof isRead === 'boolean' ? isRead : undefined,
          type: type || undefined,
        },
      }),
    ]);

    return {
      notifications,
      pagination: {
        total,
        pages: Math.ceil(total / limit),
        current: page,
      },
    };
  }

  async markAsRead(id: string, userId: string) {
    const notification = await this.prisma.adminNotification.update({
      where: { id },
      data: {
        isRead: true,
        userId,
        readAt: new Date(),
      },
    });

    this.notificationGateway.notifyUpdate(notification);
    return notification;
  }

  async markAllAsRead(userId: string) {
    await this.prisma.adminNotification.updateMany({
      where: { isRead: false },
      data: {
        isRead: true,
        userId,
        readAt: new Date(),
      },
    });

    this.notificationGateway.notifyBulkUpdate();
  }

  async createNotification(data: CreateNotificationDto) {
    const notification = await this.prisma.adminNotification.create({
      data: {
        type: data.type,
        category: data.category,
        title: data.title,
        message: data.message,
        priority: data.priority || 'NORMAL',
        metadata: data.metadata || {},
      },
    });

    this.notificationGateway.notifyNew(notification);
    return notification;
  }
}
