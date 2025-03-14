import { useNotifications } from "~/hooks/useNotifications";
import { Button } from "@/components/ui/button";
import { BellIcon } from "lucide-react";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";

export function NotificationBell() {
  const { notifications, unreadCount, markAsRead } = useNotifications();

  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button variant="ghost" size="icon" className="relative">
          <BellIcon className="h-5 w-5" />
          {unreadCount > 0 && (
            <span className="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-[10px] font-medium text-white flex items-center justify-center">
              {unreadCount > 99 ? "99+" : unreadCount}
            </span>
          )}
        </Button>
      </PopoverTrigger>

      <PopoverContent align="end" className="w-80 p-0">
        <div className="max-h-[70vh] overflow-auto divide-y">
          {notifications.length === 0 ? (
            <p className="p-4 text-sm text-gray-500 text-center">
              Aucune notification
            </p>
          ) : (
            notifications.map((notif) => (
              <div
                key={notif.id}
                className={`p-4 ${!notif.isRead ? "bg-blue-50" : ""}`}
                onClick={() => !notif.isRead && markAsRead(notif.id)}
              >
                <p className="text-sm">{notif.message}</p>
                <time className="text-xs text-gray-500 mt-1">
                  {new Date(notif.createdAt).toLocaleDateString()}
                </time>
              </div>
            ))
          )}
        </div>
      </PopoverContent>
    </Popover>
  );
}
