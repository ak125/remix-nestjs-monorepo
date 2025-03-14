import { 
  WebSocketGateway, 
  WebSocketServer, 
  OnGatewayConnection,
  OnGatewayDisconnect,
  SubscribeMessage 
} from '@nestjs/websockets';
import { Server } from 'socket.io';
import { PrismaService } from '../prisma/prisma.service';

@WebSocketGateway({
  cors: {
    origin: process.env.FRONTEND_URL,
    credentials: true
  }
})
export class StatsGateway implements OnGatewayConnection, OnGatewayDisconnect {
  @WebSocketServer()
  server: Server;

  private activeConnections = 0;

  constructor(private prisma: PrismaService) {
    setInterval(() => this.broadcastStats(), 5000);
  }

  async handleConnection() {
    this.activeConnections++;
    await this.broadcastStats();
  }

  handleDisconnect() {
    this.activeConnections--;
  }

  @SubscribeMessage('getStats')
  async handleGetStats() {
    return await this.generateStats();
  }

  private async generateStats() {
    const [todayOrders, totalOrders, revenue] = await Promise.all([
      this.prisma.order.count({
        where: {
          createdAt: {
            gte: new Date(new Date().setHours(0,0,0,0))
          }
        }
      }),
      this.prisma.order.count(),
      this.prisma.order.aggregate({
        _sum: {
          total: true
        }
      })
    ]);

    return {
      todayOrders,
      totalOrders,
      revenue: revenue._sum.total || 0,
      activeUsers: this.activeConnections
    };
  }

  private async broadcastStats() {
    const stats = await this.generateStats();
    this.server.emit('statsUpdate', stats);
  }
}
