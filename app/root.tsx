import { json } from "@remix-run/node";
import {
  Links,
  LiveReload,
  Meta,
  Outlet,
  Scripts,
  ScrollRestoration,
  useLoaderData,
  useCatch,
} from "@remix-run/react";
import styles from "./tailwind.css";
import { Analytics } from "~/components/analytics";
import { ConfigService } from "~/services/config.server";
import { getUserId } from "~/lib/session.server";
import { prisma } from "~/lib/db.server";
import { StaticMarkersService, MarkerType } from "~/services/static-markers.server";
import { checkGoneUrls } from "~/middlewares/gone-urls.server";
import { Error404 } from "~/components/error/error-404";
import { Error500 } from "~/components/error/error-500";

export const links = () => [
  { rel: "stylesheet", href: styles },
];

export async function loader({ request }) {
  // Check for gone URLs first
  await checkGoneUrls(request);

  // Get authenticated user
  const userId = await getUserId(request);
  
  let user = null;
  if (userId) {
    // Try to get admin first
    const admin = await prisma.admin.findUnique({
      where: { id: parseInt(userId) },
      select: { 
        id: true,
        login: true, 
        level: true 
      }
    });
    
    if (admin) {
      user = {
        id: admin.id.toString(),
        login: admin.login,
        role: admin.level
      };
    } else {
      // Then try regular user
      const regularUser = await prisma.user.findUnique({
        where: { id: userId },
        select: {
          id: true,
          email: true,
          role: true
        }
      });
      
      if (regularUser) {
        user = {
          id: regularUser.id,
          email: regularUser.email,
          role: regularUser.role === "ADMIN" ? 6 : 1
        };
      }
    }
  }
  
  // Get site configuration
  const configService = new ConfigService();
  const config = await configService.getSiteConfig();
  
  // Récupération des marqueurs statiques
  const markersService = new StaticMarkersService();
  const [priceLowMarkers, offersMarkers, compatibilityMarkers] = await Promise.all([
    markersService.getPriceLowMarkers(),
    markersService.getOffersMarkers(),
    markersService.getCompatibilityMarkers()
  ]);
  
  return json({
    user,
    config,
    priceLowMarkers,
    offersMarkers,
    compatibilityMarkers
  });
}

export default function App() {
  const { config } = useLoaderData();
  
  return (
    <html lang="fr">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        <Meta />
        <Links />
      </head>
      <body>
        <Outlet />
        <ScrollRestoration />
        <Scripts />
        <LiveReload />
        <Analytics />
      </body>
    </html>
  );
}

export function ErrorBoundary({ error }) {
  console.error(error);
  return (
    <html lang="fr">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        <Meta />
        <Links />
      </head>
      <body>
        <Error500 error={error} />
        <Scripts />
      </body>
    </html>
  );
}

export function CatchBoundary() {
  const caught = useCatch();
  
  return (
    <html lang="fr">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        <Meta />
        <Links />
      </head>
      <body>
        {caught.status === 404 ? (
          <Error404 />
        ) : (
          <div className="min-h-screen flex flex-col items-center justify-center">
            <h1 className="text-2xl font-bold">
              {caught.status} {caught.statusText}
            </h1>
            <p className="mt-4 text-muted-foreground">{caught.data?.message || "Une erreur s'est produite"}</p>
            <Button asChild className="mt-6">
              <Link to="/">Retour à l'accueil</Link>
            </Button>
          </div>
        )}
        <Scripts />
      </body>
    </html>
  );
}
