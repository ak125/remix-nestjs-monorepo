import { useEffect, useCallback } from 'react';
import { useUser } from '~/utils/user';
import { io, Socket } from 'socket.io-client';
import { toast } from 'sonner';

let socket: Socket | null = null;

export function useNotifications() {
  const user = useUser();

  const connect = useCallback(() => {
    if (!user || socket) return;

    socket = io(process.env.WEBSOCKET_URL!, {
      auth: { userId: user.id }
    });

    socket.on('priceAlert', (data) => {
      toast.info(
        `Le prix de ${data.modelName} a baissé de ${data.oldPrice}€ à ${data.newPrice}€!`,
        {
          action: {
            label: 'Voir',
            onClick: () => window.location.href = `/models/${data.modelId}`
          }
        }
      );
    });

    socket.on('connect_error', (error) => {
      console.error('Socket connection error:', error);
    });
  }, [user]);

  const disconnect = useCallback(() => {
    if (socket) {
      socket.disconnect();
      socket = null;
    }
  }, []);

  useEffect(() => {
    connect();
    return () => disconnect();
  }, [connect, disconnect]);

  return {
    subscribeToPriceAlerts: useCallback((modelId: number) => {
      socket?.emit('subscribe:priceAlerts', modelId);
    }, []),
    disconnect
  };
}
