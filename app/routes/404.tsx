import { Link, useCatch } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { useSiteConfig } from "~/hooks/use-site-config";

export function meta() {
  return [
    { title: "Erreur 404 - Page non trouvée" },
    { name: "description", content: "Cette page n'existe pas ou a été déplacée." },
    { name: "robots", content: "noindex, nofollow" }
  ];
}

export default function Error404() {
  const { config } = useSiteConfig();
  
  return (
    <div className="min-h-screen bg-muted/20 px-4">
      <div className="container mx-auto py-16 md:py-24 flex flex-col md:flex-row">
        <div className="md:w-1/3 text-right border-r border-muted pr-8 mb-8 md:mb-0">
          <div className="text-9xl font-bold text-primary/90">404</div>
        </div>
        
        <div className="md:w-2/3 md:pl-8">
          <h1 className="text-3xl font-bold mb-4">Erreur 404 - Page non trouvée</h1>
          <p className="text-lg text-muted-foreground mb-8">
            La page que vous recherchez n'existe pas, a été déplacée ou renommée.
            Si vous recherchez des pièces détachées automobiles, nous vous invitons à explorer notre catalogue.
          </p>
          
          <div className="space-y-6">
            <div className="bg-card p-6 rounded-lg shadow-sm">
              <h2 className="text-xl font-semibold mb-4">Vous pouvez essayer:</h2>
              <ul className="space-y-3">
                <li className="flex items-start">
                  <span className="mr-2 text-primary">•</span>
                  Vérifier l'URL pour vous assurer qu'elle est correcte
                </li>
                <li className="flex items-start">
                  <span className="mr-2 text-primary">•</span>
                  Utiliser la barre de recherche pour trouver ce que vous cherchez
                </li>
                <li className="flex items-start">
                  <span className="mr-2 text-primary">•</span>
                  Retourner à la page d'accueil et naviguer à partir de là
                </li>
              </ul>
            </div>
            
            <div className="flex flex-col sm:flex-row gap-4">
              <Button asChild size="lg" className="flex-1">
                <Link to="/">
                  Retour à l'accueil
                </Link>
              </Button>
              
              <Button variant="outline" asChild size="lg" className="flex-1">
                <Link to="/blog-pieces-auto">
                  Visiter le blog automobile
                </Link>
              </Button>
            </div>
            
            <div className="mt-6 pt-4 border-t border-muted text-sm text-muted-foreground">
              <p>
                Si vous pensez qu'il s'agit d'une erreur, veuillez{" "}
                <Link to="/contact" className="text-primary hover:underline">
                  nous contacter
                </Link>.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
