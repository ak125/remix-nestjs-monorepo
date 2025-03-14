import { json } from "@remix-run/node";
import { Link, useLoaderData } from "@remix-run/react";
import { useSiteConfig } from "~/hooks/use-site-config";
import { useState, useEffect } from "react";
import { useNavigate } from "@remix-run/react";
import { motion } from "framer-motion";
import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { Search, Undo, Sun, Moon } from "lucide-react";
import { useTheme } from "~/hooks/use-theme";
import { useSupabase } from "~/hooks/use-supabase";
import { useToast } from "~/hooks/use-toast";

export const loader = async () => {
  return json({}, { status: 410 });
};

export function meta() {
  return [
    { title: "Erreur 410 - Page supprimée" },
    { name: "description", content: "Cette page a été supprimée ou déplacée définitivement de notre site." },
    { name: "robots", content: "noindex, nofollow" }
  ];
}

export default function Error410() {
  const { config } = useSiteConfig();
  const navigate = useNavigate();
  const { theme, toggleTheme } = useTheme();
  const supabase = useSupabase();
  const { toast } = useToast();
  
  const [search, setSearch] = useState("");
  const [lastPage, setLastPage] = useState("/");
  const [suggestions, setSuggestions] = useState<Array<{ id: string; name: string }>>([]);
  const [isDeleted, setIsDeleted] = useState(false);

  useEffect(() => {
    setLastPage(document.referrer || "/");
    fetchSuggestions();
  }, []);

  async function fetchSuggestions() {
    const { data } = await supabase
      .from('products')
      .select('id, name')
      .limit(5)
      .order('viewCount', { ascending: false });

    if (data) setSuggestions(data);
  }

  async function handleSearch(e: React.FormEvent) {
    e.preventDefault();
    if (search.length < 2) return;

    navigate(`/search?q=${encodeURIComponent(search)}`);
  }

  return (
    <div className="min-h-screen bg-muted/20">
      <div className="container mx-auto px-4 py-16 md:py-24 flex flex-col md:flex-row">
        <div className="md:w-1/3 text-right border-r border-muted pr-8 mb-8 md:mb-0">
          <div className="text-8xl font-bold text-primary/90">410</div>
        </div>
        
        <div className="md:w-2/3 md:pl-8">
          <h1 className="text-3xl font-bold mb-4">Erreur 410 - Page supprimée</h1>
          <p className="text-lg text-muted-foreground mb-6">
            Cette page a été définitivement supprimée ou déplacée. 
            Si vous recherchez des pièces détachées automobiles, nous vous invitons à explorer notre catalogue.
          </p>
          
          <div className="space-y-6">
            <div className="bg-card p-6 rounded-lg shadow-sm">
              <h2 className="text-xl font-semibold mb-3">Où souhaitez-vous aller ?</h2>
              <div className="space-y-2">
                <div>
                  <Link 
                    to="/" 
                    className="text-primary hover:underline"
                  >
                    {config.domainName} - Accueil
                  </Link>
                </div>
                <div>
                  <Link 
                    to="/blog-pieces-auto" 
                    className="text-primary hover:underline"
                  >
                    Blog automobile
                  </Link>
                </div>
                <div>
                  <Link 
                    to="/contact" 
                    className="text-primary hover:underline"
                  >
                    Contactez-nous
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="space-y-8"
          >
            <div className="text-center space-y-4">
              <motion.h1 
                initial={{ scale: 0.9 }}
                animate={{ scale: 1 }}
                className="text-4xl font-bold"
              >
                Page supprimée
              </motion.h1>
              <p className="text-muted-foreground">
                Cette page n'est plus disponible. Utilisez la recherche ou consultez nos suggestions.
              </p>
            </div>

            <form onSubmit={handleSearch} className="flex gap-2">
              <Input
                type="search"
                placeholder="Rechercher une pièce..."
                value={search}
                onChange={e => setSearch(e.target.value)}
                className="flex-1"
              />
              <Button type="submit">
                <Search className="h-4 w-4" />
              </Button>
            </form>

            {suggestions.length > 0 && (
              <motion.div
                initial={{ height: 0, opacity: 0 }}
                animate={{ height: 'auto', opacity: 1 }}
                className="bg-muted rounded-lg p-4"
              >
                <h2 className="font-medium mb-2">Suggestions populaires</h2>
                <ul className="space-y-2">
                  {suggestions.map(item => (
                    <li key={item.id}>
                      <Link 
                        to={`/products/${item.id}`}
                        className="text-blue-600 hover:underline"
                      >
                        {item.name}
                      </Link>
                    </li>
                  ))}
                </ul>
              </motion.div>
            )}

            <div className="flex justify-center gap-4">
              <Button variant="outline" onClick={() => navigate(-1)}>
                Retour
              </Button>
              <Button onClick={toggleTheme} variant="ghost">
                {theme === 'dark' ? <Sun className="h-4 w-4" /> : <Moon className="h-4 w-4" />}
              </Button>
              {isDeleted && (
                <Button 
                  onClick={() => {
                    setIsDeleted(false);
                    toast({ title: "Action annulée" });
                  }}
                  variant="default"
                >
                  <Undo className="h-4 w-4 mr-2" />
                  Annuler
                </Button>
              )}
            </div>
          </motion.div>
        </div>
      </div>
    </div>
  );
}
