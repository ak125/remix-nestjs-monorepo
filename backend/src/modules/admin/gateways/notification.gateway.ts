import {
  WebSocketGateway,
  WebSocketServer,
  OnGatewayConnection,
  OnGatewayDisconnect,
} from '@nestjs/websockets';
import { Server, Socket } from 'socket.io';
import { Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';

@WebSocketGateway({
  namespace: 'admin',
  cors: {
    origin: process.env.FRONTEND_URL,
    credentials: true,
  },
})
export class AdminNotificationGateway implements OnGatewayConnection, OnGatewayDisconnect {
  @WebSocketServer()
  server: Server;

  private readonly logger = new Logger(AdminNotificationGateway.name);
  private readonly adminSockets = new Map<string, Socket>();

  constructor(private readonly prisma: PrismaService) {}

  handleConnection(client: Socket) {
    const adminId = client.handshake.auth.adminId;
    if (adminId) {
      this.adminSockets.set(adminId, client);
      this.logger.log(`Admin connecté: ${adminId}`);
    }
  }

  handleDisconnect(client: Socket) {
    const adminId = client.handshake.auth.adminId;
    if (adminId) {
      this.adminSockets.delete(adminId);
      this.logger.log(`Admin déconnecté: ${adminId}`);
    }
  }

  async createNotification(data: {
    type: string;
    title: string;
    message: string;
    metadata?: Record<string, any>;
  }) {
    const notification = await this.prisma.adminNotification.create({
      data: {
        type: data.type,
        title: data.title,
        message: data.message,
        metadata: data.metadata || {},
      },
    });

    // Notifier tous les admins connectés
    this.server.emit('notification.new', notification);

    return notification;
  }
}
