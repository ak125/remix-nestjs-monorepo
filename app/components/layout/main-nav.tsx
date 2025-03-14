import { Link, useLocation } from "@remix-run/react";
import { useSiteConfig } from "~/hooks/use-site-config";
import { cn } from "~/lib/utils";
import { Sheet, SheetContent, SheetTrigger } from "~/components/ui/sheet";
import { Button } from "~/components/ui/button";
import { Menu } from "lucide-react";

interface NavLink {
  href: string;
  label: string;
}

export function MainNav() {
  const { config } = useSiteConfig();
  const location = useLocation();

  const navLinks: NavLink[] = [
    { href: "/", label: "Accueil" },
    { href: "/blog/entretien", label: "Entretien" },
    { href: "/blog/constructeurs", label: "Constructeurs" },
    { href: "/blog/guide", label: "Guide" }
  ];

  return (
    <nav className="bg-primary text-primary-foreground sticky top-0 z-40">
      <div className="container mx-auto flex items-center justify-between h-16">
        {/* Logo */}
        <Link 
          to="/" 
          className="font-bold text-lg tracking-wider"
        >
          AUTOMECANIK
        </Link>

        {/* Desktop Navigation */}
        <div className="hidden md:flex items-center space-x-6">
          {navLinks.map(link => (
            <Link
              key={link.href}
              to={link.href}
              className={cn(
                "text-sm font-medium transition-colors hover:text-primary-foreground/80",
                location.pathname === link.href && "underline"
              )}
            >
              {link.label}
            </Link>
          ))}
        </div>

        {/* Mobile Menu Trigger */}
        <Sheet>
          <SheetTrigger asChild>
            <Button variant="ghost" size="icon" className="md:hidden">
              <Menu className="h-5 w-5" />
              <span className="sr-only">Menu</span>
            </Button>
          </SheetTrigger>
          <SheetContent side="left" className="w-[240px] sm:w-[300px]">
            <nav className="flex flex-col gap-4 mt-8">
              {navLinks.map(link => (
                <Link
                  key={link.href}
                  to={link.href}
                  className="block py-2 text-foreground hover:text-primary"
                >
                  {link.label}
                </Link>
              ))}
            </nav>
          </SheetContent>
        </Sheet>
      </div>
    </nav>
  );
}
