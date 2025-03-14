import { useEffect, useState } from 'react';
import { useSocket } from '@/hooks/useSocket';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Badge } from '@/components/ui/badge';
import { formatDateRelative } from '@/lib/format';

interface Notification {
  id: string;
  type: string;
  title: string;
  message: string;
  isRead: boolean;
  createdAt: string;
}

export default function Notifications() {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const socket = useSocket('admin');

  useEffect(() => {
    fetchNotifications();

    socket.on('notification.new', handleNewNotification);
    socket.on('notification.update', handleNotificationUpdate);
    socket.on('notification.bulk-update', fetchNotifications);

    return () => {
      socket.off('notification.new');
      socket.off('notification.update');
      socket.off('notification.bulk-update');
    };
  }, []);

  const fetchNotifications = async () => {
    const res = await fetch('/api/admin/notifications');
    const data = await res.json();
    setNotifications(data.notifications);
    setUnreadCount(data.notifications.filter(n => !n.isRead).length);
  };

  const handleNewNotification = (notification: Notification) => {
    setNotifications(prev => [notification, ...prev]);
    setUnreadCount(count => count + 1);
  };

  const handleNotificationUpdate = (updated: Notification) => {
    setNotifications(prev => 
      prev.map(n => n.id === updated.id ? updated : n)
    );
    if (updated.isRead) {
      setUnreadCount(count => count - 1);
    }
  };

  const markAsRead = async (id: string) => {
    await fetch(`/api/admin/notifications/${id}/read`, {
      method: 'PATCH',
    });
  };

  const markAllAsRead = async () => {
    await fetch('/api/admin/notifications/read-all', {
      method: 'PATCH',
    });
  };

  return (
    <Card className="w-96">
      <div className="p-4 border-b flex justify-between items-center">
        <div className="flex items-center gap-2">
          <h3 className="font-medium">Notifications</h3>
          {unreadCount > 0 && (
            <Badge variant="secondary">{unreadCount}</Badge>
          )}
        </div>
        {unreadCount > 0 && (
          <Button 
            variant="ghost" 
            size="sm"
            onClick={markAllAsRead}
          >
            Tout marquer comme lu
          </Button>
        )}
      </div>

      <ScrollArea className="h-[480px]">
        <div className="divide-y">
          {notifications.map(notification => (
            <div
              key={notification.id}
              className={`p-4 hover:bg-gray-50 transition-colors ${
                !notification.isRead ? 'bg-blue-50/50' : ''
              }`}
              onClick={() => markAsRead(notification.id)}
            >
              <div className="flex justify-between items-start mb-1">
                <h4 className="font-medium">{notification.title}</h4>
                <span className="text-xs text-gray-500">
                  {formatDateRelative(notification.createdAt)}
                </span>
              </div>
              <p className="text-sm text-gray-600">
                {notification.message}
              </p>
            </div>
          ))}
        </div>
      </ScrollArea>
    </Card>
  );
}
