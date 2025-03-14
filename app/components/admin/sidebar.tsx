import { useEffect, useState } from "react";
import { Link, useLocation, useFetcher } from "@remix-run/react";
import { cn } from "~/lib/utils";
import {
  Menu,
  X,
  Home,
  ShoppingCart,
  Users,
  Settings,
  BarChart3,
  Search,
  LogOut,
  Box,
  Landmark,
  FileText
} from "lucide-react";
import { Button } from "~/components/ui/button";

interface SidebarProps {
  className?: string;
  onClose?: () => void;
}

interface AdminUser {
  id: number;
  login: string;
  level: number;
  firstName?: string;
  lastName?: string;
}

interface NavigationItem {
  name: string;
  to: string;
  icon: React.ReactNode;
  minLevel: number;
}

export function Sidebar({ className, onClose }: SidebarProps) {
  const location = useLocation();
  const [user, setUser] = useState<AdminUser | null>(null);
  const fetcher = useFetcher<{ user: AdminUser }>();
  
  // Fetch user data
  useEffect(() => {
    if (fetcher.state === "idle" && !fetcher.data) {
      fetcher.load("/api/admin/session");
    }
    
    if (fetcher.data?.user) {
      setUser(fetcher.data.user);
    }
  }, [fetcher]);
  
  // Navigation items with required privilege levels
  const navigationItems: NavigationItem[] = [
    { name: "Tableau de bord", to: "/admin/dashboard", icon: <Home size={20} />, minLevel: 1 },
    { name: "Commandes", to: "/admin/orders", icon: <ShoppingCart size={20} />, minLevel: 2 },
    { name: "Clients", to: "/admin/users", icon: <Users size={20} />, minLevel: 2 },
    { name: "Catalogue", to: "/admin/products", icon: <Box size={20} />, minLevel: 3 },
    { name: "Stocks", to: "/admin/stock", icon: <Landmark size={20} />, minLevel: 4 },
    { name: "Statistiques", to: "/admin/statistics", icon: <BarChart3 size={20} />, minLevel: 5 },
    { name: "Factures", to: "/admin/invoices", icon: <FileText size={20} />, minLevel: 5 },
    { name: "SEO", to: "/admin/seo", icon: <Search size={20} />, minLevel: 7 },
    { name: "Paramètres", to: "/admin/settings", icon: <Settings size={20} />, minLevel: 9 },
  ];
  
  // Filter navigation items based on user level
  const filteredNavigation = user 
    ? navigationItems.filter(item => user.level >= item.minLevel)
    : [];

  return (
    <div 
      className={cn(
        "flex flex-col h-full bg-gray-800 text-white",
        className
      )}
    >
      {/* Logo & Close Button */}
      <div className="flex items-center justify-between p-4 border-b border-gray-700">
        <div className="flex items-center space-x-2">
          <img src="/assets/img/mini-logo-icon-gray.png" alt="Logo" className="w-8 h-8" />
          <span className="text-xl font-semibold">AutoMecanik</span>
        </div>
        {onClose && (
          <Button 
            variant="ghost" 
            size="sm"
            onClick={onClose}
            className="text-gray-400 hover:text-white"
          >
            <X size={20} />
          </Button>
        )}
      </div>
      
      {/* User Info */}
      <div className="p-4 border-b border-gray-700">
        <div className="text-sm">
          <p className="text-gray-400">Connecté en tant que</p>
          <p className="font-medium">{user?.firstName || user?.login || "Chargement..."}</p>
          {user && <p className="text-xs text-gray-400">Niveau {user.level}</p>}
        </div>
      </div>
      
      {/* Navigation */}
      <nav className="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
        {filteredNavigation.map((item) => {
          const isActive = location.pathname.startsWith(item.to);
          
          return (
            <Link
              key={item.name}
              to={item.to}
              className={cn(
                "flex items-center px-3 py-2 rounded-md text-sm",
                isActive
                  ? "bg-gray-900 text-white"
                  : "text-gray-300 hover:bg-gray-700 hover:text-white"
              )}
            >
              <span className="mr-3">{item.icon}</span>
              <span>{item.name}</span>
            </Link>
          );
        })}
      </nav>
      
      {/* Footer Actions */}
      <div className="p-4 border-t border-gray-700">
        <Link
          to="/admin/logout"
          className="flex items-center px-3 py-2 rounded-md text-sm text-red-400 hover:bg-gray-700 hover:text-red-300"
        >
          <LogOut size={20} className="mr-3" />
          <span>Déconnexion</span>
        </Link>
        <div className="mt-4 text-center text-xs text-gray-500">
          &copy; {new Date().getFullYear()} AutoMecanik
        </div>
      </div>
    </div>
  );
}
