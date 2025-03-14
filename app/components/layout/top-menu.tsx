import { Link } from "@remix-run/react";
import { useSiteConfig } from "~/hooks/use-site-config";
import { useUser } from "~/hooks/use-user";
import { Phone } from "lucide-react";

export function TopMenu() {
  const { config } = useSiteConfig();
  const { isAuthenticated, user } = useUser();

  return (
    <div className="bg-muted py-2 px-4 hidden lg:block border-b">
      <div className="container mx-auto flex justify-between items-center">
        <span className="text-sm text-muted-foreground">
          Pièces auto à prix pas cher
        </span>
        
        <div className="flex items-center gap-4">
          <a 
            href={`tel:${config.sitePhoneToCall}`} 
            className="text-sm text-primary hover:underline flex items-center gap-1"
          >
            <Phone className="h-3 w-3" />
            <span>{config.sitePhone}</span>
          </a>
          
          <span className="text-muted-foreground">|</span>
          
          {isAuthenticated ? (
            <Link to="/compte" className="text-sm text-primary hover:underline">
              {user?.firstName} {user?.lastName}
            </Link>
          ) : (
            <div className="flex items-center gap-4">
              <Link to="/connexion" className="text-sm text-primary hover:underline">
                Entrer
              </Link>
              <span className="text-muted-foreground">|</span>
              <Link to="/inscription" className="text-sm text-primary hover:underline">
                S'inscrire
              </Link>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
