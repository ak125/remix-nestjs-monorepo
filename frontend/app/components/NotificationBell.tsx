import { useEffect } from 'react';
import { useFetcher } from '@remix-run/react';

export default function NotificationBell() {
  const fetcher = useFetcher();

  useEffect(() => {
    if ('serviceWorker' in navigator && 'Notification' in window) {
      Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
          registerPushSubscription();
        }
      });
    }
  }, []);

  async function registerPushSubscription() {
    const registration = await navigator.serviceWorker.register('/sw.js');
    const subscription = await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: process.env.VAPID_PUBLIC_KEY
    });

    fetcher.submit(
      { subscription: JSON.stringify(subscription) },
      { method: 'post', action: '/api/push/register' }
    );
  }

  return (
    <button 
      className="relative p-2"
      onClick={() => fetcher.load('/api/notifications')}
    >
      <span className="sr-only">Notifications</span>
      <span className="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full" />
      🔔
    </button>
  );
}
