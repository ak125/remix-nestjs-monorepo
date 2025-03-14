import { useState, useEffect } from "react";
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from "~/components/ui/sheet";
import { Button } from "~/components/ui/button";
import { Menu, X } from "lucide-react";
import { cn } from "~/lib/utils";
import { NavLink } from "@remix-run/react";
import { useSidebarStore } from "~/stores/sidebar";

interface NavItem {
  label: string;
  href: string;
  icon?: React.ReactNode;
}

const mainNav: NavItem[] = [
  { label: "Tableau de bord", href: "/dashboard" },
  { label: "Catalogue", href: "/catalogue" },
  { label: "Commandes", href: "/orders" },
  { label: "Stock", href: "/stock" }
];

export function Sidebar() {
  const [mounted, setMounted] = useState(false);
  const { isOpen, setIsOpen } = useSidebarStore();
  
  useEffect(() => {
    setMounted(true);
  }, []);

  if (!mounted) return null;

  return (
    <>
      <Sheet open={isOpen} onOpenChange={setIsOpen}>
        <SheetTrigger asChild>
          <Button
            variant="ghost"
            size="icon"
            className="fixed top-4 left-4 z-50 lg:hidden"
          >
            <Menu className="h-6 w-6" />
          </Button>
        </SheetTrigger>
        <SheetContent side="left" className="p-0 w-72">
          <SidebarContent />
        </SheetContent>
      </Sheet>

      <div className="hidden lg:block fixed inset-y-0 left-0 w-72 border-r bg-background">
        <SidebarContent />
      </div>
    </>
  );
}

function SidebarContent() {
  return (
    <div className="h-full flex flex-col">
      <SheetHeader className="p-6 border-b">
        <SheetTitle>Menu Principal</SheetTitle>
      </SheetHeader>
      
      <nav className="flex-1 p-4 space-y-2">
        {mainNav.map((item) => (
          <NavLink
            key={item.href}
            to={item.href}
            className={({ isActive }) =>
              cn(
                "flex items-center gap-3 px-4 py-2 rounded-md text-sm transition-colors",
                isActive 
                  ? "bg-primary text-primary-foreground" 
                  : "hover:bg-muted"
              )
            }
          >
            {item.icon}
            {item.label}
          </NavLink>
        ))}
      </nav>

      <div className="p-4 border-t">
        <Button 
          variant="outline" 
          className="w-full"
          onClick={() => {/* Logout logic */}}
        >
          Déconnexion
        </Button>
      </div>
    </div>
  );
}
