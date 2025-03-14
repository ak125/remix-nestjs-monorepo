import { io, Socket } from 'socket.io-client';

interface OrderUpdate {
  orderId: string;
  lineId: number;
  previousStatus: number;
  newStatus: number;
  piece?: {
    name: string;
    ref: string;
  };
}

class SocketService {
  private socket: Socket | null = null;
  private readonly listeners = new Map<string, Set<(data: any) => void>>();

  connect(sessionId: string) {
    if (this.socket?.connected) return;

    this.socket = io(process.env.NEXT_PUBLIC_WS_URL || 'http://localhost:3000', {
      auth: { sessionId },
      path: '/orders',
      withCredentials: true,
      reconnection: true,
      reconnectionDelay: 1000,
      reconnectionAttempts: 5,
    });

    // Configuration des événements de base
    this.setupBaseEvents();
    
    // Configuration des événements métier
    this.setupBusinessEvents();
  }

  private setupBaseEvents() {
    if (!this.socket) return;

    this.socket.on('connect', () => {
      console.log('✅ Connecté au WebSocket');
    });

    this.socket.on('disconnect', (reason) => {
      console.log('❌ Déconnecté:', reason);
    });

    this.socket.on('error', (error: Error) => {
      console.error('🚨 Erreur:', error);
    });

    this.socket.on('connect_error', (error) => {
      console.error('🚨 Erreur de connexion:', error);
    });
  }

  private setupBusinessEvents() {
    if (!this.socket) return;

    this.socket.on('orderUpdated', (data: OrderUpdate) => {
      console.log('📦 Mise à jour commande:', data);
      this.notifyListeners('orderUpdated', data);
    });

    this.socket.on('orderCancelled', ({ orderId }) => {
      console.log(`❌ Commande #${orderId} annulée`);
      this.notifyListeners('orderCancelled', { orderId });
    });
  }

  // Suivre une commande
  watchOrder(orderId: string) {
    this.socket?.emit('watchOrder', orderId);
  }

  // Ne plus suivre une commande
  unwatchOrder(orderId: string) {
    this.socket?.emit('unwatchOrder', orderId);
  }

  // Mise à jour statut
  updateOrderStatus(orderId: string, lineId: number, statusId: number) {
    if (!this.socket?.connected) {
      console.error('❌ Non connecté');
      return false;
    }

    this.socket.emit('updateOrderStatus', {
      orderId,
      lineId,
      statusId,
    });

    return true;
  }

  // Gestion des listeners
  on(event: string, callback: (data: any) => void) {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, new Set());
    }
    this.listeners.get(event)?.add(callback);

    return () => this.off(event, callback);
  }

  off(event: string, callback: (data: any) => void) {
    this.listeners.get(event)?.delete(callback);
  }

  private notifyListeners(event: string, data: any) {
    this.listeners.get(event)?.forEach(callback => callback(data));
  }

  disconnect() {
    if (this.socket) {
      this.socket.disconnect();
      this.socket = null;
      this.listeners.clear();
    }
  }
}

// Export singleton
export const socketService = new SocketService();
