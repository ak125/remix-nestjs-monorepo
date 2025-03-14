import { Link, useRouteError } from "@remix-run/react";
import { Alert, AlertDescription, AlertTitle } from "~/components/ui/alert";
import { Button } from "~/components/ui/button";
import { cn } from "~/lib/utils";

interface ErrorDisplay {
  status: number;
  title: string;
  description: string;
  buttonText: string;
}

const errorDisplays: Record<number, ErrorDisplay> = {
  410: {
    status: 410,
    title: "Page supprimée",
    description: "Cette page n'existe plus. Essayez de chercher une autre pièce.",
    buttonText: "Retour au catalogue"
  },
  412: {
    status: 412,
    title: "Page indisponible",
    description: "Cette page est temporairement indisponible. Veuillez réessayer plus tard.",
    buttonText: "Retour à l'accueil"
  }
};

export default function ErrorPage() {
  const error = useRouteError() as { status?: number };
  const display = errorDisplays[error?.status || 500] || {
    status: 500,
    title: "Erreur inattendue",
    description: "Une erreur est survenue. Veuillez réessayer plus tard.",
    buttonText: "Retour à l'accueil"
  };

  return (
    <div className="flex items-center justify-center min-h-screen bg-gray-50">
      <div className="w-full max-w-lg px-4">
        <Alert 
          className={cn(
            "p-6 text-center bg-white shadow-lg rounded-lg border-2",
            error?.status === 410 ? "border-amber-500" : "border-red-500"
          )}
        >
          <AlertTitle className="text-xl mb-2">
            Erreur {display.status} - {display.title}
          </AlertTitle>
          <AlertDescription className="text-gray-600 mb-6">
            {display.description}
          </AlertDescription>
          <div className="flex gap-4 justify-center">
            <Link to="/">
              <Button variant="outline">
                {display.buttonText}
              </Button>
            </Link>
            <Button 
              variant="ghost"
              onClick={() => window.location.reload()}
            >
              Réessayer
            </Button>
          </div>
        </Alert>
      </div>
    </div>
  );
}
