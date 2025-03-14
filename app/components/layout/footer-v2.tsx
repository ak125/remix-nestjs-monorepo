import { useState, useEffect } from "react";
import { Link } from "@remix-run/react";
import { useSiteConfig } from "~/hooks/use-site-config";
import { Button } from "~/components/ui/button";
import { ChevronUp } from "lucide-react";
import { Input } from "~/components/ui/input";

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
  const [email, setEmail] = useState("");

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

  const handleNewsletterSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // Implement newsletter signup logic here
    console.log("Newsletter signup:", email);
    // Reset form
    setEmail("");
    // Show success message
    alert("Merci pour votre inscription à notre newsletter!");
  };

  return (
    <footer className="bg-gray-900 text-gray-200">
      <div className="container mx-auto px-4 py-10">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* Brand Section */}
          <div>
            <h3 className="font-bold text-lg text-white mb-4">Automecanik</h3>
            <p className="text-sm text-gray-400 mb-4">
              Pièces détachées auto à un prix pas cher sur Automecanik.com
            </p>
            <Link 
              to="/blog-pieces-auto/comment-choisir-le-bon-produit" 
              className="text-blue-400 hover:underline text-sm"
              target="_blank"
              rel="noopener noreferrer"
            >
              Achat de pièces de voiture en ligne
            </Link>
          </div>
          
          {/* Menus Section */}
          <div>
            <h3 className="font-bold text-lg text-white mb-4">Informations</h3>
            <ul className="space-y-2">
              {menus.map(menu => (
                <li key={menu.id}>
                  <Link 
                    to={`/${menu.alias}`}
                    className="text-gray-400 hover:text-white transition-colors"
                    rel={menu.relFollow ? "follow" : "nofollow"}
                  >
                    {menu.title}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
          
          {/* Newsletter Section */}
          <div className="text-right">
            <h3 className="font-bold text-lg text-white mb-4">Inscription newsletter</h3>
            <p className="text-sm text-gray-400 mb-4">
              Découvrez comme il est simple et rapide de rester informé sur les dernières nouveautés.
            </p>
            
            <form onSubmit={handleNewsletterSubmit} className="mt-4">
              <div className="flex gap-2">
                <Input
                  type="email"
                  placeholder="Votre email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  className="bg-gray-800 border-gray-700 text-white"
                />
                <Button type="submit" className="bg-blue-500 hover:bg-blue-600">
                  S'inscrire
                </Button>
              </div>
            </form>
          </div>
        </div>
        
        {/* Copyright */}
        <div className="mt-12 border-t border-gray-800 pt-6 flex flex-col md:flex-row justify-between text-sm text-gray-500">
          <p>&copy; {new Date().getFullYear()} - tous droits réservés Automecanik.com</p>
          <p className="mt-2 md:mt-0">
            Powered by{" "}
            <a 
              href={config.owner.domain}
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-400 hover:text-white"
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
          className="fixed bottom-6 right-6 rounded-full shadow-lg bg-blue-500 hover:bg-blue-600"
          onClick={scrollToTop}
          aria-label="Remonter en haut"
        >
          <ChevronUp className="h-5 w-5 text-white" />
        </Button>
      )}
    </footer>
  );
}
