import { Link } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { AlertTriangle, RefreshCw, Home } from "lucide-react";

interface Error500Props {
  error?: Error;
}

export function Error500({ error }: Error500Props) {
  const isDev = process.env.NODE_ENV === 'development';
  
  return (
    <div className="min-h-screen bg-muted/20 px-4">
      <div className="container mx-auto py-16 md:py-24 flex flex-col md:flex-row">
        <div className="md:w-1/3 text-right border-r border-muted pr-8 mb-8 md:mb-0">
          <div className="text-9xl font-bold text-destructive/80">500</div>
        </div>
        
        <div className="md:w-2/3 md:pl-8">
          <div className="flex items-center gap-2 mb-4">
            <AlertTriangle className="text-destructive h-6 w-6" />
            <h1 className="text-3xl font-bold">Erreur serveur</h1>
          </div>
          
          <p className="text-lg text-muted-foreground mb-8">
            Une erreur s'est produite lors du traitement de votre demande. 
            Nos équipes techniques ont été notifiées et travaillent à résoudre le problème.
          </p>
          
          {isDev && error && (
            <div className="bg-destructive/10 border border-destructive/30 p-4 rounded-md mb-6">
              <div className="text-sm font-semibold mb-2">Détails de l'erreur (environnement de développement uniquement)</div>
              <pre className="text-xs overflow-auto p-2 bg-background/80 rounded">{error.message}</pre>
              {error.stack && (
                <pre className="text-xs overflow-auto p-2 mt-2 bg-background/80 rounded max-h-64">
                  {error.stack}
                </pre>
              )}
            </div>
          )}
          
          <div className="flex flex-col sm:flex-row gap-4">
            <Button 
              onClick={() => window.location.reload()} 
              size="lg" 
              className="flex-1 flex items-center gap-2"
            >
              <RefreshCw className="w-4 h-4" />
              <span>Rafraîchir la page</span>
            </Button>
            
            <Button 
              variant="outline" 
              asChild 
              size="lg" 
              className="flex-1 flex items-center gap-2"
            >
              <Link to="/">
                <Home className="w-4 h-4" />
                <span>Retour à l'accueil</span>
              </Link>
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
}
