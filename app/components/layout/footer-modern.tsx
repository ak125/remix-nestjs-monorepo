import { useState, useEffect } from "react";
import { Link, useFetcher } from "@remix-run/react";
import { useSiteConfig } from "~/hooks/use-site-config";
import { SubscribeForm } from "~/components/newsletter/subscribe-form";
import { Button } from "~/components/ui/button";
import { ChevronUp } from "lucide-react";

interface FooterMenuItem {
  id: number;
  title: string;
  alias: string;
  level: number;
  relFollow: boolean;
}

interface FooterProps {
  className?: string;
}

export function FooterModern({ className = "" }: FooterProps) {
  const { config } = useSiteConfig();
  const fetcher = useFetcher<FooterMenuItem[]>();
  const [showScrollToTop, setShowScrollToTop] = useState(false);

  useEffect(() => {
    if (fetcher.state === "idle" && !fetcher.data) {
      fetcher.load("/api/footer-menus");
    }
    
    const handleScroll = () => {
      setShowScrollToTop(window.scrollY > 300);
    };
    
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, [fetcher]);
  
  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  // Group menus by level
  const policyMenus = fetcher.data?.filter(menu => menu.level === 1) || [];
  const supportMenus = fetcher.data?.filter(menu => menu.level === 2) || [];
  const productMenus = fetcher.data?.filter(menu => menu.level === 3) || [];

  return (
    <footer className={`bg-slate-900 text-slate-300 ${className}`}>
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
          {/* Logo and About */}
          <div>
            <h3 className="text-xl font-bold mb-4 text-white">
              {config.domainName}
            </h3>
            <p className="text-sm mb-4">
              Pièces détachées auto à un prix pas cher sur {config.domainName}.com
            </p>
            <Link 
              to="/blog-pieces-auto/comment-choisir-le-bon-produit" 
              className="text-blue-400 hover:text-blue-300 transition-colors text-sm"
            >
              Achat de pièces de voiture en ligne
            </Link>
          </div>
          
          {/* Policies */}
          {policyMenus.length > 0 && (
            <div>
              <h3 className="text-lg font-bold mb-4 text-white">Politiques</h3>
              <ul className="space-y-2">
                {policyMenus.map(menu => (
                  <li key={menu.id}>
                    <Link 
                      to={`/${menu.alias}`}
                      className="text-slate-300 hover:text-white transition-colors text-sm"
                      rel={menu.relFollow ? undefined : "nofollow"}
                    >
                      {menu.title}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          )}
          
          {/* Support */}
          {supportMenus.length > 0 && (
            <div>
              <h3 className="text-lg font-bold mb-4 text-white">Support</h3>
              <ul className="space-y-2">
                {supportMenus.map(menu => (
                  <li key={menu.id}>
                    <Link 
                      to={`/${menu.alias}`}
                      className="text-slate-300 hover:text-white transition-colors text-sm"
                      rel={menu.relFollow ? undefined : "nofollow"}
                    >
                      {menu.title}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          )}
          
          {/* Newsletter */}
          <div>
            <h3 className="text-lg font-bold mb-4 text-white">Newsletter</h3>
            <p className="text-sm mb-4">
              Découvrez comme il est simple et rapide de rester informé sur les dernières nouveautés.
            </p>
            <SubscribeForm compact={true} className="mt-2" />
          </div>
        </div>
        
        {/* Bottom bar with copyright */}
        <div className="mt-12 pt-6 border-t border-slate-700/50 flex flex-col md:flex-row justify-between items-center text-sm">
          <p>
            &copy; {new Date().getFullYear()}{" "}
            <span className="font-semibold">{config.domainName}.com</span> - 
            Tous droits réservés
          </p>
          
          <p className="mt-2 md:mt-0">
            Powered by{" "}
            <a 
              href={config.owner.domain}
              className="hover:text-white"
              target="_blank" 
              rel="noopener noreferrer"
            >
              {config.owner.name}
            </a>
          </p>
        </div>
      </div>
      
      {/* Scroll to top button */}
      {showScrollToTop && (
        <Button
          variant="default"
          size="icon"
          className="fixed bottom-6 right-6 rounded-full shadow-lg bg-blue-600 hover:bg-blue-700 z-50"
          onClick={scrollToTop}
          aria-label="Retour en haut de page"
        >
          <ChevronUp className="h-5 w-5" />
        </Button>
      )}
    </footer>
  );
}
