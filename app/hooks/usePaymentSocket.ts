import { useEffect, useCallback } from 'react';
import { io, Socket } from 'socket.io-client';
import { useToast } from '@/components/ui/toast';

export function usePaymentSocket(userId: string) {
  const { toast } = useToast();

  const handlePaymentUpdate = useCallback((data: any) => {
    const variant = data.type === 'payment.success' ? 'default' : 'destructive';
    toast({
      title: data.type === 'payment.success' ? 'Paiement accepté' : 'Paiement refusé',
      description: data.message || data.error,
      variant,
    });
  }, [toast]);

  useEffect(() => {
    const socket = io(process.env.SOCKET_URL || 'http://localhost:3000', {
      auth: { userId },
    });

    socket.on('payment.update', handlePaymentUpdate);

    return () => {
      socket.off('payment.update', handlePaymentUpdate);
      socket.disconnect();
    };
  }, [userId, handlePaymentUpdate]);
}
