import { useState, useEffect } from "react";
import { Sidebar } from "./sidebar";
import { Button } from "~/components/ui/button";
import { Menu } from "lucide-react";
import { useLocation } from "@remix-run/react";

export function MobileSidebar() {
  const [isOpen, setIsOpen] = useState(false);
  const location = useLocation();
  
  // Close sidebar on route changes
  useEffect(() => {
    setIsOpen(false);
  }, [location.pathname]);
  
  // Prevent scrolling when sidebar is open
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'auto';
    }
    
    return () => {
      document.body.style.overflow = 'auto';
    };
  }, [isOpen]);

  return (
    <>
      {/* Trigger Button */}
      <Button
        onClick={() => setIsOpen(true)}
        variant="outline"
        size="icon"
        className="fixed top-4 left-4 z-40 lg:hidden"
      >
        <Menu className="h-[1.2rem] w-[1.2rem]" />
        <span className="sr-only">Menu</span>
      </Button>
      
      {/* Sidebar & Overlay */}
      {isOpen && (
        <>
          {/* Backdrop overlay */}
          <div 
            className="fixed inset-0 bg-black/50 z-40 lg:hidden"
            onClick={() => setIsOpen(false)}
          />
          
          {/* Sidebar */}
          <div className="fixed inset-y-0 left-0 z-50 w-64 lg:hidden">
            <Sidebar onClose={() => setIsOpen(false)} />
          </div>
        </>
      )}
    </>
  );
}
