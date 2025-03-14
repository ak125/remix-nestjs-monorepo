import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { AdminGateway } from '../gateways/admin.gateway';

interface CreateAlertDto {
  title: string;
  message: string;
  type: string;
  severity: 'info' | 'warning' | 'error';
  metadata?: Record<string, any>;
}

@Injectable()
export class AdminAlertsService {
  private readonly logger = new Logger(AdminAlertsService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly adminGateway: AdminGateway,
  ) {}

  async createAlert(data: CreateAlertDto) {
    const alert = await this.prisma.adminAlert.create({
      data: {
        title: data.title,
        message: data.message,
        type: data.type,
        severity: data.severity,
        metadata: data.metadata || {},
      },
    });

    // Notifier en temps réel
    this.adminGateway.broadcast('alert.new', alert);

    return alert;
  }

  async getAlerts(params: {
    isRead?: boolean;
    type?: string;
    page?: number;
    limit?: number;
  }) {
    const { isRead, type, page = 1, limit = 20 } = params;

    const [alerts, total] = await Promise.all([
      this.prisma.adminAlert.findMany({
        where: {
          isRead: typeof isRead === 'boolean' ? isRead : undefined,
          type: type || undefined,
        },
        orderBy: { createdAt: 'desc' },
        take: limit,
        skip: (page - 1) * limit,
      }),
      this.prisma.adminAlert.count({
        where: {
          isRead: typeof isRead === 'boolean' ? isRead : undefined,
          type: type || undefined,
        },
      }),
    ]);

    return {
      alerts,
      pagination: {
        total,
        pages: Math.ceil(total / limit),
        current: page,
      },
    };
  }

  async markAsRead(alertId: string, adminId: string) {
    const alert = await this.prisma.adminAlert.update({
      where: { id: alertId },
      data: {
        isRead: true,
        readBy: adminId,
        readAt: new Date(),
      },
    });

    this.adminGateway.broadcast('alert.updated', alert);
    return alert;
  }
}
