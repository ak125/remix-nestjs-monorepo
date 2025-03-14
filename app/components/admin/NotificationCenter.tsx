import { useEffect, useState } from 'react';
import { useSocket } from '@/hooks/useSocket';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ScrollArea } from '@/components/ui/scroll-area';
import { formatDateRelative } from '@/lib/format';

interface Notification {
  id: string;
  type: string;
  title: string;
  message: string;
  metadata: Record<string, any>;
  isRead: boolean;
  createdAt: string;
}

export default function NotificationCenter() {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const socket = useSocket('admin', { autoConnect: true });

  useEffect(() => {
    // Charger les notifications existantes
    fetch('/api/admin/notifications')
      .then(r => r.json())
      .then(data => {
        setNotifications(data.notifications);
        setUnreadCount(data.notifications.filter(n => !n.isRead).length);
      });

    // Écouter les nouvelles notifications
    socket.on('notification.new', (notification: Notification) => {
      setNotifications(prev => [notification, ...prev]);
      setUnreadCount(count => count + 1);
    });

    return () => {
      socket.off('notification.new');
    };
  }, []);

  const markAsRead = async (id: string) => {
    const response = await fetch(`/api/admin/notifications/${id}/read`, {
      method: 'PATCH',
    });

    if (response.ok) {
      setNotifications(prev => 
        prev.map(n => n.id === id ? { ...n, isRead: true } : n)
      );
      setUnreadCount(count => count - 1);
    }
  };

  return (
    <Card className="w-96">
      <div className="p-4 border-b flex justify-between items-center">
        <h3 className="font-medium">Notifications</h3>
        {unreadCount > 0 && (
          <Badge variant="secondary">{unreadCount} nouvelle(s)</Badge>
        )}
      </div>

      <ScrollArea className="h-96">
        <div className="divide-y">
          {notifications.map((notification) => (
            <div
              key={notification.id}
              className={`p-4 hover:bg-gray-50 cursor-pointer ${
                !notification.isRead ? 'bg-blue-50' : ''
              }`}
              onClick={() => markAsRead(notification.id)}
            >
              <div className="flex justify-between items-start mb-1">
                <h4 className="font-medium">{notification.title}</h4>
                <span className="text-xs text-gray-500">
                  {formatDateRelative(notification.createdAt)}
                </span>
              </div>
              <p className="text-sm text-gray-600">{notification.message}</p>
            </div>
          ))}
        </div>
      </ScrollArea>
    </Card>
  );
}
