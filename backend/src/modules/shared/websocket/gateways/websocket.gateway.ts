import { 
  WebSocketGateway as NestWebSocketGateway, 
  WebSocketServer,
  OnGatewayInit,
  OnGatewayConnection,
  OnGatewayDisconnect,
} from '@nestjs/websockets';
import { Server, Socket } from 'socket.io';
import { LoggerService } from '../../../common/services/logger.service';
import { ConnectionService } from '../services/connection.service';

interface WebSocketEvent {
  type: string;
  payload: any;
  timestamp: Date;
}

@NestWebSocketGateway({
  cors: {
    origin: process.env.FRONTEND_URL || 'http://localhost:3000',
    credentials: true,
  },
  namespace: 'ws',
})
export class WebSocketGateway implements OnGatewayInit, OnGatewayConnection, OnGatewayDisconnect {
  @WebSocketServer()
  server: Server;

  constructor(
    private readonly connectionService: ConnectionService,
    private readonly logger: LoggerService,
  ) {}

  afterInit() {
    this.logger.log('WebSocket Gateway initialisé', 'WebSocket');
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

  async emitToSession(sessionId: string, event: string, data: any) {
    try {
      const eventData: WebSocketEvent = {
        type: event,
        payload: data,
        timestamp: new Date(),
      };

      this.server.to(`session:${sessionId}`).emit(event, eventData);
      
      this.logger.debug('Événement émis', 'WebSocket', {
        event,
        sessionId,
        data: eventData,
      });

    } catch (error) {
      this.logger.error('Erreur émission événement', 'WebSocket', error);
    }
  }

  async broadcast(event: string, data: any, rooms?: string[]) {
    try {
      const eventData: WebSocketEvent = {
        type: event,
        payload: data,
        timestamp: new Date(),
      };

      let target = this.server;
      if (rooms?.length) {
        target = target.to(rooms);
      }

      target.emit(event, eventData);

      this.logger.debug('Broadcast envoyé', 'WebSocket', {
        event,
        rooms,
        data: eventData,
      });

    } catch (error) {
      this.logger.error('Erreur broadcast', 'WebSocket', error);
    }
  }
}
