import { useState, useRef, useEffect } from 'react';
import { useSocket } from '@/hooks/useSocket';
import { Bell } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
import { AlertList } from './AlertList';

export default function AlertBell() {
  const [unreadCount, setUnreadCount] = useState(0);
  const [isOpen, setIsOpen] = useState(false);
  const audioRef = useRef<HTMLAudioElement>(null);
  const socket = useSocket('admin');

  useEffect(() => {
    fetchUnreadCount();

    socket.on('alert.new', (alert) => {
      setUnreadCount(prev => prev + 1);
      playSound();
    });

    socket.on('alert.updated', () => {
      fetchUnreadCount();
    });

    return () => {
      socket.off('alert.new');
      socket.off('alert.updated');
    };
  }, []);

  const fetchUnreadCount = async () => {
    const res = await fetch('/api/admin/alerts/unread-count');
    const data = await res.json();
    setUnreadCount(data.count);
  };

  const playSound = () => {
    if (audioRef.current) {
      audioRef.current.currentTime = 0;
      audioRef.current.play();
    }
  };

  return (
    <>
      <audio 
        ref={audioRef}
        src="/sounds/notification.mp3" 
        preload="auto"
      />

      <Popover open={isOpen} onOpenChange={setIsOpen}>
        <PopoverTrigger asChild>
          <Button 
            variant="ghost" 
            size="icon"
            className="relative"
          >
            <Bell className="h-5 w-5" />
            {unreadCount > 0 && (
              <span className="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-medium text-white ring-2 ring-white">
                {unreadCount > 99 ? '99+' : unreadCount}
              </span>
            )}
          </Button>
        </PopoverTrigger>
        
        <PopoverContent 
          align="end" 
          className="w-80 p-0"
        >
          <AlertList onAlertRead={fetchUnreadCount} />
        </PopoverContent>
      </Popover>
    </>
  );
}
