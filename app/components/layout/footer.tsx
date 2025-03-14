import { useState, useEffect } from "react";
import { Link } from "@remix-run/react";
import { useSiteConfig } from "~/hooks/use-site-config";
import { Button } from "~/components/ui/button";
import { ChevronUp } from "lucide-react";

interface FooterMenuItem {
  id: number;
  title: string;
  alias: string;
  level: number;
  relFollow: boolean;
}

export function Footer() {
  const { config } = useSiteConfig();
  const [menus, setMenus] = useState<FooterMenuItem[]>([]);
  const [showScrollToTop, setShowScrollToTop] = useState(false);

  useEffect(() => {
    fetch("/api/footer")
      .then(res => res.json())
      .then(data => setMenus(data))
      .catch(err => console.error("Error loading footer menus:", err));
      
    const handleScroll = () => {
      setShowScrollToTop(window.scrollY > 300);
    };
    
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);
  
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth"
    });
  };

  const policiesMenu = menus.filter(menu => menu.level === 1);
  const supportMenu = menus.filter(menu => menu.level === 2);

  return (
    <footer className="bg-muted-foreground/90 text-muted-foreground-foreground py-12">
      <div className="container mx-auto px-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Brand */}
          <div>
            <h3 className="font-bold text-lg text-primary-foreground mb-4">automecanik</h3>
            <p className="text-sm text-primary-foreground/80 mb-4">
              Pièces détachées auto à un prix pas cher sur Automecanik.com
            </p>
            <Link 
              to="/blog-pieces-auto/guide/pieces-auto-comment-s-y-retrouver"
              className="text-primary hover:underline text-sm"
              target="_blank"
              rel="noopener noreferrer"
            >
              Achat de pièces de voiture en ligne
            </Link>
          </div>
          
          {/* Policies */}
          <div>
            <h3 className="font-bold text-lg text-primary-foreground mb-4">politiques</h3>
            <div className="space-y-2">
              {policiesMenu.map(menu => (
                <p key={menu.id}>
                  <Link 
                    to={`/${menu.alias}`}
                    className="text-primary hover:underline text-sm"
                    rel={menu.relFollow ? "follow" : "nofollow"}
                  >
                    {menu.title}
                  </Link>
                </p>
              ))}
            </div>
          </div>
          
          {/* Support */}
          <div>
            <h3 className="font-bold text-lg text-primary-foreground mb-4">Support</h3>
            <div className="space-y-2">
              {supportMenu.map(menu => (
                <p key={menu.id}>
                  <Link 
                    to={`/${menu.alias}`}
                    className="text-primary hover:underline text-sm"
                    rel={menu.relFollow ? "follow" : "nofollow"}
                  >
                    {menu.title}
                  </Link>
                </p>
              ))}
            </div>
          </div>
          
          {/* Newsletter */}
          <div>
            <h3 className="font-bold text-lg text-primary-foreground mb-4">inscription newsletter</h3>
            <p className="text-sm text-primary-foreground/80">
              Découvrez comme il est simple et rapide de rester informé sur les dernières nouveautés.
            </p>
          </div>
        </div>
        
        {/* Copyright */}
        <div className="mt-12 border-t border-muted-foreground/20 pt-6 flex flex-col md:flex-row justify-between text-sm text-primary-foreground/70">
          <p>&copy; {new Date().getFullYear()} - tous droits réservés Automecanik.com</p>
          <p className="mt-2 md:mt-0">
            Powered by{" "}
            <a 
              href={config.owner.domain}
              target="_blank"
              rel="noopener noreferrer"
              className="text-primary hover:underline"
            >
              {config.owner.name}
            </a>
          </p>
        </div>
      </div>
      
      {/* Scroll to top button */}
      {showScrollToTop && (
        <Button
          variant="secondary"
          size="icon"
          className="fixed bottom-6 right-6 rounded-full shadow-lg"
          onClick={scrollToTop}
          aria-label="Remonter en haut"
        >
          <ChevronUp className="h-5 w-5" />
        </Button>
      )}
    </footer>
  );
}
