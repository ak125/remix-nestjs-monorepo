import { Link, useLocation } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from "~/components/ui/dropdown-menu";
import {
  Sheet,
  SheetTrigger,
  SheetContent,
  SheetHeader,
  SheetTitle,
} from "~/components/ui/sheet";
import { Menu, ShoppingCart, ChevronDown } from "lucide-react";
import { cn } from "~/lib/utils";

interface NavItem {
  href: string;
  label: string;
}

const navItems: NavItem[] = [
  { href: "/", label: "Accueil" },
  { href: "/catalogue", label: "Catalogue produit" },
  { href: "/blog", label: "Blog automobile" },
];

export function Navbar() {
  const location = useLocation();

  return (
    <nav className="bg-primary text-primary-foreground">
      <div className="container mx-auto">
        <div className="flex items-center justify-between h-16 px-4">
          {/* Logo */}
          <Link to="/" className="flex items-center space-x-2">
            <img 
              src="/images/logo.png" 
              alt="Automecanik" 
              className="h-8"
            />
          </Link>

          {/* Navigation Desktop */}
          <div className="hidden md:flex items-center space-x-6">
            {navItems.map(item => (
              <Link
                key={item.href}
                to={item.href}
                className={cn(
                  "text-sm font-medium transition-colors hover:text-primary-foreground/80",
                  location.pathname === item.href && "text-white"
                )}
              >
                {item.label}
              </Link>
            ))}
          </div>

          {/* Actions */}
          <div className="flex items-center space-x-4">
            <Link to="/panier">
              <Button variant="ghost" size="icon">
                <ShoppingCart className="h-5 w-5" />
              </Button>
            </Link>

            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" className="hidden md:flex items-center gap-2">
                  Mon compte
                  <ChevronDown className="h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuItem asChild>
                  <Link to="/connexion">Connexion</Link>
                </DropdownMenuItem>
                <DropdownMenuItem asChild>
                  <Link to="/inscription">Inscription</Link>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            {/* Menu Mobile */}
            <Sheet>
              <SheetTrigger asChild>
                <Button variant="ghost" size="icon" className="md:hidden">
                  <Menu className="h-5 w-5" />
                </Button>
              </SheetTrigger>
              <SheetContent side="left">
                <SheetHeader>
                  <SheetTitle>Menu</SheetTitle>
                </SheetHeader>
                <div className="mt-6 flex flex-col space-y-4">
                  {navItems.map(item => (
                    <Link
                      key={item.href}
                      to={item.href}
                      className="text-foreground/70 hover:text-foreground transition-colors"
                    >
                      {item.label}
                    </Link>
                  ))}
                  <Link to="/connexion" className="text-foreground/70 hover:text-foreground">
                    Connexion
                  </Link>
                  <Link to="/inscription" className="text-foreground/70 hover:text-foreground">
                    Inscription
                  </Link>
                </div>
              </SheetContent>
            </Sheet>
          </div>
        </div>
      </div>
    </nav>
  );
}
