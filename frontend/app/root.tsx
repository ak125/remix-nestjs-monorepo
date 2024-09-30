// Utilisation de `import type` pour les types
import { type RemixService } from "@fafa/backend";

// Import des composants React
import {
  json,
  type LinksFunction,
  type LoaderFunctionArgs,
} from "@remix-run/node"; // Utilisation de `import type`
import {
  Links,
  Meta,
  Outlet,
  Scripts,
  ScrollRestoration,
  useRouteLoaderData, // Ajout de `useRouteLoaderData`
} from "@remix-run/react";

// Import du fichier CSS et types Remix
import { Navbar } from "./components/Navbar";
import stylesheet from "./global.css";
import logo from "./routes/_assets/automecanik-logo.png";
import { getOptionalUser } from "./server/auth.server";

export const links: LinksFunction = () => [
  { rel: "stylesheet", href: stylesheet },
];

export const loader = async ({ request, context }: LoaderFunctionArgs) => {
  const user = await getOptionalUser({ context });
  return json({
    user, // Correction : passer `user` au lieu de `User`
  });
};

export const useOptionalUser = () => {
  const data = useRouteLoaderData<typeof loader>("root"); // Correction de la syntaxe de `useRouteLoaderData`

  if (!data?.user) {
    throw new Error("Root loader is missing user data"); // Correction de la syntaxe de l'erreur
  }

  return data.user;
};

// Ce bloc déclare un module pour étendre le contexte Remix
declare module "@remix-run/node" {
  interface AppLoadContext {
    remixService: RemixService;
  }
}

export function Layout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className="min-h-screen">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <Meta />
        <Links />
      </head>
      <body className="min-h-full">
        <Navbar logo={logo} />
        {children}
        <ScrollRestoration />
        <Scripts />
      </body>
    </html>
  );
}

export default function App() {
  return <Outlet />;
}
