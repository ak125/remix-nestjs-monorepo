import { 
  WebSocketGateway, 
  WebSocketServer, 
  SubscribeMessage,
  OnGatewayConnection,
  OnGatewayDisconnect 
} from '@nestjs/websockets';
import { Server, Socket } from 'socket.io';
import { PrismaService } from '../prisma/prisma.service';
import { Logger } from '@nestjs/common';

@WebSocketGateway({
  cors: {
    origin: process.env.FRONTEND_URL,
    credentials: true
  }
})
export class NotificationGateway implements OnGatewayConnection, OnGatewayDisconnect {
  private readonly logger = new Logger(NotificationGateway.name);

  @WebSocketServer()
  server: Server;

  constructor(private prisma: PrismaService) {}

  async handleConnection(client: Socket) {
    const userId = client.handshake.auth.userId;
    if (userId) {
      await this.prisma.webSocketConnection.create({
        data: {
          userId: parseInt(userId),
          socketId: client.id
        }
      });
      
      client.join(`user:${userId}`);
      this.logger.log(`Client connected: ${client.id} for user: ${userId}`);
    }
  }

  async handleDisconnect(client: Socket) {
    await this.prisma.webSocketConnection.delete({
      where: { socketId: client.id }
    });
    this.logger.log(`Client disconnected: ${client.id}`);
  }

  async notifyUser(userId: string, event: string, data: any) {
    this.server.to(`user:${userId}`).emit(event, data);
  }

  async broadcastNotification(event: string, data: any) {
    this.server.emit(event, data);
  }

  @SubscribeMessage('subscribe:priceAlerts')
  async handlePriceAlertSubscription(client: Socket, modelId: number) {
    const userId = client.handshake.auth.userId;
    if (userId) {
      await this.prisma.priceAlert.create({
        data: {
          userId,
          modelId
        }
      });
    }
  }
}
