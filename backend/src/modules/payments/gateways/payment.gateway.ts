import { 
  WebSocketGateway, 
  WebSocketServer,
  OnGatewayConnection,
  OnGatewayDisconnect 
} from '@nestjs/websockets';
import { Server, Socket } from 'socket.io';
import { Logger } from '@nestjs/common';

@WebSocketGateway({
  cors: {
    origin: process.env.FRONTEND_URL,
    credentials: true
  }
})
export class PaymentGateway implements OnGatewayConnection, OnGatewayDisconnect {
  @WebSocketServer()
  server: Server;

  private readonly logger = new Logger(PaymentGateway.name);
  private readonly userSockets = new Map<string, Socket>();

  handleConnection(client: Socket) {
    const userId = client.handshake.auth.userId;
    if (userId) {
      this.userSockets.set(userId, client);
      this.logger.log(`Client connecté: ${userId}`);
    }
  }

  handleDisconnect(client: Socket) {
    const userId = client.handshake.auth.userId;
    if (userId) {
      this.userSockets.delete(userId);
      this.logger.log(`Client déconnecté: ${userId}`);
    }
  }

  notifyPaymentUpdate(userId: string, data: any) {
    const socket = this.userSockets.get(userId);
    if (socket) {
      socket.emit('payment.update', data);
    }
  }

  notifyPaymentSuccess(userId: string, orderId: string) {
    this.notifyPaymentUpdate(userId, {
      type: 'payment.success',
      orderId,
      message: 'Paiement accepté'
    });
  }

  notifyPaymentFailure(userId: string, orderId: string, error?: string) {
    this.notifyPaymentUpdate(userId, {
      type: 'payment.failed',
      orderId,
      error: error || 'Paiement refusé',
    });
  }
}
