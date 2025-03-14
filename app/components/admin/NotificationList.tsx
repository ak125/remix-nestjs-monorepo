import { useState, useEffect } from 'react';
import { useSocket } from '@/hooks/useSocket';
import { Button } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { formatDateRelative } from '@/lib/format';

const CATEGORIES = {
  PAYMENT: { label: 'Paiements', icon: '💳' },
  REFUND: { label: 'Remboursements', icon: '💰' },
  ERROR: { label: 'Erreurs', icon: '⚠️' },
  ORDER: { label: 'Commandes', icon: '📦' },
} as const;

interface NotificationListProps {
  onNotificationRead?: () => void;
}

export function NotificationList({ onNotificationRead }: NotificationListProps) {
  const [notifications, setNotifications] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState<string>('');
  const socket = useSocket('admin');

  useEffect(() => {
    fetchNotifications();

    socket.on('notification.new', (notification) => {
      setNotifications(prev => [notification, ...prev]);
    });

    return () => {
      socket.off('notification.new');
    };
  }, []);

  const fetchNotifications = async () => {
    const res = await fetch('/api/admin/notifications?limit=20');
    const data = await res.json();
    setNotifications(data.notifications);
  };

  const markAsRead = async (id: string) => {
    await fetch(`/api/admin/notifications/${id}/read`, {
      method: 'PATCH',
    });
    onNotificationRead?.();
  };

  const markAllAsRead = async () => {
    await fetch('/api/admin/notifications/read-all', {
      method: 'PATCH',
    });
    onNotificationRead?.();
  };

  const filteredNotifications = selectedCategory 
    ? notifications.filter(n => n.category === selectedCategory)
    : notifications;

  return (
    <div>
      <div className="flex items-center justify-between p-4 border-b">
        <h3 className="font-medium">Notifications</h3>
        <select
          value={selectedCategory}
          onChange={(e) => setSelectedCategory(e.target.value)}
          className="text-sm border rounded px-2 py-1"
        >
          <option value="">Toutes</option>
          {Object.entries(CATEGORIES).map(([key, { label, icon }]) => (
            <option key={key} value={key}>
              {icon} {label}
            </option>
          ))}
        </select>
        {notifications.some(n => !n.isRead) && (
          <Button 
            variant="ghost" 
            size="sm"
            onClick={markAllAsRead}
          >
            Tout marquer comme lu
          </Button>
        )}
      </div>

      <ScrollArea className="h-[400px]">
        {notifications.length > 0 ? (
          <div className="divide-y">
            {filteredNotifications.map(notification => (
              <div
                key={notification.id}
                className={`p-4 hover:bg-gray-50 ${
                  !notification.isRead ? 'bg-blue-50/50' : ''
                } ${notification.priority === 'URGENT' ? 'border-l-4 border-red-500' : ''}`}
              >
                <div className="flex items-start gap-3">
                  <span className="text-xl">
                    {CATEGORIES[notification.category]?.icon}
                  </span>
                  <div className="flex-1">
                    <h4 className="font-medium">{notification.title}</h4>
                    <p className="text-sm text-gray-600">{notification.message}</p>
                    <div className="flex gap-2 mt-2 text-xs text-gray-500">
                      <time>{formatDateRelative(notification.createdAt)}</time>
                      {notification.priority === 'URGENT' && (
                        <span className="text-red-500 font-medium">URGENT</span>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <p className="text-center text-gray-500 p-4">
            Aucune notification
          </p>
        )}
      </ScrollArea>
    </div>
  );
}
