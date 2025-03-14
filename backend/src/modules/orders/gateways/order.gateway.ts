import {
  WebSocketGateway as NestWebSocketGateway,
  WebSocketServer,
  OnGatewayInit,
  OnGatewayConnection,
  OnGatewayDisconnect,
  SubscribeMessage,
  MessageBody,
  ConnectedSocket,
} from '@nestjs/websockets';
import { Server, Socket } from 'socket.io';
import { LoggerService } from '../../../common/services/logger.service';
import { ConnectionService } from '../../websocket/services/connection.service';

interface OrderEvent {
  orderId: string;
  lineId: number;
  previousStatus?: number;
  newStatus: number;
  piece?: {
    id?: string;
    name?: string;
    ref?: string;
  };
  timestamp?: Date;
}

interface EquivalenceEvent extends OrderEvent {
  ticket?: {
    id: number;
    amount: number;
  };
}

@NestWebSocketGateway({
  namespace: 'orders',
  cors: {
    origin: process.env.FRONTEND_URL || 'http://localhost:3000',
    credentials: true,
  },
})
export class OrderGateway implements OnGatewayInit, OnGatewayConnection, OnGatewayDisconnect {
  @WebSocketServer()
  server: Server;

  constructor(
    private readonly connectionService: ConnectionService,
    private readonly logger: LoggerService,
  ) {}

  afterInit() {
    this.logger.log('Gateway Orders initialisée', 'WebSocket');
  }

  async handleConnection(client: Socket) {
    const sessionId = client.handshake.auth.sessionId;
    if (!sessionId) {
      this.logger.error('Connexion refusée: pas de session', 'WebSocket');
      client.disconnect();
      return;
    }

    try {
      await this.connectionService.addConnection(sessionId, client.id);
      client.join(`session:${sessionId}`);
      
      this.logger.log('Client connecté', 'WebSocket', {
        clientId: client.id,
        sessionId,
      });

    } catch (error) {
      this.logger.error('Erreur connexion client', 'WebSocket', error);
      client.disconnect();
    }
  }

  async handleDisconnect(client: Socket) {
    try {
      await this.connectionService.removeConnection(client.id);
      this.logger.log('Client déconnecté', 'WebSocket', { clientId: client.id });
    } catch (error) {
      this.logger.error('Erreur déconnexion client', 'WebSocket', error);
    }
  }

  // NOTIFICATIONS ORDRES

  async notifyOrderUpdate(event: OrderEvent, sessionId: string) {
    try {
      const eventData = {
        ...event,
        timestamp: new Date(),
      };

      this.server.to(`session:${sessionId}`).emit('orderUpdated', eventData);
      
      this.logger.debug('Notification commande envoyée', 'WebSocket', {
        event: eventData,
        sessionId,
      });

    } catch (error) {
      this.logger.error('Erreur notification commande', 'WebSocket', error);
    }
  }

  // ÉQUIVALENCES

  async notifyEquivalenceProposed(event: EquivalenceEvent, sessionId: string) {
    try {
      const eventData = {
        ...event,
        timestamp: new Date(),
      };

      this.server.to(`session:${sessionId}`).emit('equivalenceProposed', eventData);
      
      this.logger.debug('Notification équivalence proposée', 'WebSocket', {
        event: eventData,
        sessionId, 
      });

    } catch (error) {
      this.logger.error('Erreur notification équivalence', 'WebSocket', error);
    }
  }

  async notifyEquivalenceValidated(event: EquivalenceEvent, sessionId: string) {
    try {
      const eventData = {
        ...event,
        timestamp: new Date(),
      };

      this.server.to(`session:${sessionId}`).emit('equivalenceValidated', eventData);

      this.logger.debug('Notification validation équivalence', 'WebSocket', {
        event: eventData,
        sessionId,
      });

    } catch (error) {
      this.logger.error('Erreur notification validation', 'WebSocket', error);
    }
  }

  // SOUSCRIPTIONS CLIENTS

  @SubscribeMessage('subscribeToOrder')
  async handleSubscribeOrder(
    @MessageBody() orderId: string,
    @ConnectedSocket() client: Socket,
  ) {
    try {
      client.join(`order:${orderId}`);
      
      this.logger.debug('Client souscrit à la commande', 'WebSocket', {
        clientId: client.id,
        orderId,
      });

    } catch (error) {
      this.logger.error('Erreur souscription commande', 'WebSocket', error);
    }
  }

  @SubscribeMessage('unsubscribeFromOrder') 
  async handleUnsubscribeOrder(
    @MessageBody() orderId: string,
    @ConnectedSocket() client: Socket,
  ) {
    try {
      client.leave(`order:${orderId}`);
      
      this.logger.debug('Client désinscrit de la commande', 'WebSocket', {
        clientId: client.id,
        orderId,
      });

    } catch (error) {
      this.logger.error('Erreur désinscription commande', 'WebSocket', error);
    }
  }
}
