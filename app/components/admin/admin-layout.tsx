import { Link, useLocation, useNavigate } from "@remix-run/react";
import {
  LayoutDashboard,
  ShoppingCart,
  Users,
  Settings,
  Search,
  BarChart,
  LogOut,
  Menu,
  X
} from "lucide-react";
import { Button } from "~/components/ui/button";
import { useState, useEffect } from "react";
import { cn } from "~/lib/utils";

interface AdminLayoutProps {
  children: React.ReactNode;
}

interface NavItem {
  title: string;
  href: string;
  icon: React.ReactNode;
  minLevel?: number;
}

export function AdminLayout({ children }: AdminLayoutProps) {
  const location = useLocation();
  const navigate = useNavigate();
  const [isMobileNavOpen, setIsMobileNavOpen] = useState(false);
  
  // Close mobile nav when location changes
  useEffect(() => {
    setIsMobileNavOpen(false);
  }, [location]);

  const handleLogout = async () => {
    await fetch("/api/admin/logout", { method: "POST" });
    navigate("/admin/login");
  };
  
  const navItems: NavItem[] = [
    {
      title: "Dashboard",
      href: "/admin/dashboard",
      icon: <LayoutDashboard className="h-5 w-5" />,
      minLevel: 1
    },
    {
      title: "Commandes",
      href: "/admin/orders",
      icon: <ShoppingCart className="h-5 w-5" />,
      minLevel: 2
    },
    {
      title: "Clients",
      href: "/admin/customers",
      icon: <Users className="h-5 w-5" />,
      minLevel: 2
    },
    {
      title: "SEO",
      href: "/admin/seo",
      icon: <Search className="h-5 w-5" />,
      minLevel: 7
    },
    {
      title: "Statistiques",
      href: "/admin/stats",
      icon: <BarChart className="h-5 w-5" />,
      minLevel: 5
    },
    {
      title: "Paramètres",
      href: "/admin/settings",
      icon: <Settings className="h-5 w-5" />,
      minLevel: 9
    }
  ];

  return (
    <div className="flex h-screen bg-gray-100">
      {/* Mobile nav toggle */}
      <div className="lg:hidden fixed top-4 left-4 z-50">
        <Button 
          variant="outline" 
          size="icon" 
          onClick={() => setIsMobileNavOpen(!isMobileNavOpen)}
        >
          {isMobileNavOpen ? (
            <X className="h-5 w-5" />
          ) : (
            <Menu className="h-5 w-5" />
          )}
        </Button>
      </div>

      {/* Sidebar */}
      <aside 
        className={cn(
          "w-64 bg-white border-r transition-all duration-300 ease-in-out",
          isMobileNavOpen ? "fixed inset-y-0 left-0 z-40" : "fixed -left-64 lg:left-0 inset-y-0 z-40"
        )}
      >
        <div className="p-4 border-b flex items-center justify-between">
          <h1 className="text-xl font-bold">Administration</h1>
          <Button 
            variant="ghost" 
            size="icon" 
            className="lg:hidden" 
            onClick={() => setIsMobileNavOpen(false)}
          >
            <X className="h-5 w-5" />
          </Button>
        </div>
        
        <nav className="p-4 space-y-1">
          {navItems.map((item) => (
            <Link 
              key={item.href} 
              to={item.href}
              className={cn(
                "flex items-center px-4 py-2 rounded-md text-sm",
                location.pathname.startsWith(item.href) 
                  ? "bg-gray-100 text-primary font-medium" 
                  : "text-gray-600 hover:bg-gray-50"
              )}
            >
              {item.icon}
              <span className="ml-3">{item.title}</span>
            </Link>
          ))}
          
          <button 
            onClick={handleLogout}
            className="flex items-center px-4 py-2 rounded-md text-sm text-gray-600 hover:bg-gray-50 w-full text-left"
          >
            <LogOut className="h-5 w-5" />
            <span className="ml-3">Déconnexion</span>
          </button>
        </nav>
      </aside>

      {/* Mobile overlay */}
      {isMobileNavOpen && (
        <div 
          className="fixed inset-0 bg-black/20 z-30 lg:hidden"
          onClick={() => setIsMobileNavOpen(false)}
        />
      )}

      {/* Main content */}
      <div className="flex-1 flex flex-col overflow-hidden lg:ml-64">
        <main className="flex-1 overflow-y-auto p-6">
          {children}
        </main>
      </div>
    </div>
  );
}
