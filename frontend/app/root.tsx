import { type RemixService } from "@fafa/backend";
import { type LinksFunction, type LoaderFunctionArgs, json } from "@remix-run/node";
import { Link, Links, Meta, Outlet, Scripts, ScrollRestoration, useLoaderData } from "@remix-run/react";

import { Navbar } from "./components/Navbar";
import stylesheet from "./global.css?url";
import logo from "./routes/_assets/logo-automecanik-dark.png";
import { Footer } from "./components/ui/Footer";

export const links: LinksFunction = () => [
  { rel: "stylesheet", href: stylesheet },
];

export const loader = async ({ context }: LoaderFunctionArgs) => {
  return json({
    message: context.remixService.getHello()
  });
};

declare module "@remix-run/node" {
  interface AppLoadContext {
    fafa: string;
    remixService: RemixService; // Changed from 'any' to 'RemixService'
  }
}

export function Layout({ children }: { children: React.ReactNode }) {
  useLoaderData<typeof loader>();
  return (
    <html lang="en" className="h-full">
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <Meta />
        <Links />
      </head>
      <body className="h-full bg-gray-100">
        <div className="min-h-screen flex flex-col">
          <Navbar logo={logo} />
          <main className="flex-grow flex flex-col">
            <div className="flex-grow">
              {children}
            </div>
           </main>
        </div>
        <Footer />
        <ScrollRestoration />
        <Scripts />
      </body>
    </html>
  );
}

export default function App() {
  return <Outlet />;
}