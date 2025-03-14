import { useEffect, useState } from "react";
import { io } from "socket.io-client";
import { AnimatePresence, motion } from "framer-motion";

const SOCKET_URL = process.env.API_URL || 'http://localhost:3000';

interface StockAlert {
  id: string;
  type: 'low_stock' | 'movement';
  message: string;
  stockId: string;
  createdAt: string;
}

export function StockNotifications() {
  const [alerts, setAlerts] = useState<StockAlert[]>([]);

  useEffect(() => {
    const socket = io(SOCKET_URL);

    socket.on('stockAlert', (alert: StockAlert) => {
      setAlerts(prev => [alert, ...prev]);
    });

    return () => {
      socket.disconnect();
    };
  }, []);

  async function dismissAlert(id: string) {
    await fetch(`/api/stock/notifications/${id}/dismiss`, {
      method: 'POST'
    });
    setAlerts(prev => prev.filter(alert => alert.id !== id));
  }

  if (alerts.length === 0) return null;

  return (
    <div className="fixed bottom-4 right-4 max-w-sm w-full">
      <AnimatePresence>
        {alerts.map(alert => (
          <motion.div
            key={alert.id}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 20 }}
            className={`mb-2 p-4 rounded-lg shadow-lg ${
              alert.type === 'low_stock' 
                ? 'bg-red-50 text-red-800'
                : 'bg-blue-50 text-blue-800'
            }`}
          >
            <div className="flex justify-between items-start">
              <div>
                <p className="font-medium">{alert.message}</p>
                <span className="text-sm opacity-75">
                  {new Date(alert.createdAt).toLocaleString()}
                </span>
              </div>
              <button
                onClick={() => dismissAlert(alert.id)}
                className="text-current opacity-50 hover:opacity-100"
              >
                ×
              </button>
            </div>
          </motion.div>
        ))}
      </AnimatePresence>
    </div>
  );
}
