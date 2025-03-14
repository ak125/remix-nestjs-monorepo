import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';

@Injectable()
export class ConnectionService {
  private readonly logger = new Logger(ConnectionService.name);

  constructor(private readonly prisma: PrismaService) {}

  async addConnection(sessionId: string, socketId: string) {
    try {
      // Vérifier la session
      const session = await this.prisma.xTR_SESSION.findFirst({
        where: { session_id: sessionId },
      });

      if (!session) {
        this.logger.warn(`Session invalide: ${sessionId}`);
        return null;
      }

      // Créer la connexion
      const connection = await this.prisma.xTR_WEBSOCKET_CONNECTION.create({
        data: {
          session_id: sessionId,
          socket_id: socketId,
          connected_at: new Date(),
        },
      });

      this.logger.log(`Nouvelle connexion: ${socketId} (Session: ${sessionId})`);
      return connection;

    } catch (error) {
      this.logger.error('Erreur création connexion:', error);
      throw error;
    }
  }

  async removeConnection(socketId: string) {
    try {
      await this.prisma.xTR_WEBSOCKET_CONNECTION.delete({
        where: { socket_id: socketId },
      });

      this.logger.log(`Connexion supprimée: ${socketId}`);

    } catch (error) {
      this.logger.error('Erreur suppression connexion:', error);
      throw error;
    }
  }

  async getConnectionsBySession(sessionId: string) {
    try {
      return await this.prisma.xTR_WEBSOCKET_CONNECTION.findMany({
        where: { session_id: sessionId },
        orderBy: { connected_at: 'desc' },
      });

    } catch (error) {
      this.logger.error('Erreur récupération connexions:', error);
      throw error;
    }
  }

  async cleanupStaleConnections(maxAgeHours = 24) {
    try {
      const staleDate = new Date();
      staleDate.setHours(staleDate.getHours() - maxAgeHours);

      const { count } = await this.prisma.xTR_WEBSOCKET_CONNECTION.deleteMany({
        where: {
          connected_at: {
            lt: staleDate,
          },
        },
      });

      this.logger.log(`${count} connexions obsolètes nettoyées`);
      return count;

    } catch (error) {
      this.logger.error('Erreur nettoyage connexions:', error);
      throw error;
    }
  }
}
