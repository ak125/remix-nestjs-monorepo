// Utilisation de `import type` pour les types
import { type RemixService } from "@fafa/backend";
import { type LinksFunction } from "@remix-run/node";

// Import des composants React
import {
  Links,
  Meta,
  Outlet,
  Scripts,
  ScrollRestoration,
} from "@remix-run/react";

// Import du fichier CSS
import stylesheet from "./global.css";

export const links: LinksFunction = () => [
  { rel: "stylesheet", href: stylesheet },
];

// Ce bloc déclare un module pour étendre le contexte Remix
declare module "@remix-run/node" {
  interface AppLoadContext {
    fafa: string;
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
      <body className='min-h-full'>
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
