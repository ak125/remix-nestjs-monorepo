import { useEffect, useCallback, useState } from 'react';
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

export function useOrderSocket(sessionId: string) {
  const [isConnected, setIsConnected] = useState(false);
  const [lastUpdate, setLastUpdate] = useState<OrderUpdate | null>(null);

  const connectSocket = useCallback(() => {
    const socket = io(process.env.NEXT_PUBLIC_WS_URL || 'http://localhost:3000', {
      auth: { sessionId },
      path: '/orders',
      withCredentials: true,
      transports: ['websocket'],
      reconnection: true,
    });

    socket.on('connect', () => {
      console.log('✅ Connecté au serveur WebSocket');
      setIsConnected(true);
    });

    socket.on('disconnect', () => {
      console.log('❌ Déconnexion WebSocket');
      setIsConnected(false);
    });

    socket.on('orderUpdated', (data: OrderUpdate) => {
      console.log('📦 Mise à jour commande:', data);
      setLastUpdate(data);
    });

    socket.on('orderCancelled', ({ orderId }) => {
      console.log(`❌ Commande #${orderId} annulée`);
    });

    socket.on('paymentReceived', ({ orderId, amount }) => {
      console.log(`💰 Paiement reçu - Commande #${orderId}: ${amount}€`);
    });

    socket.on('error', (error: Error) => {
      console.error('🚨 Erreur WebSocket:', error);
    });

    return socket;
  }, [sessionId]);

  useEffect(() => {
    if (!sessionId) return;

    const socket = connectSocket();
    
    return () => {
      socket.disconnect();
    };
  }, [sessionId, connectSocket]);

  return {
    isConnected,
    lastUpdate,
  };
}
