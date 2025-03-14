import { type RemixService } from "@fafa/backend";
import { type LinksFunction, type LoaderFunctionArgs, json } from "@remix-run/node";
import { Links, Meta, Outlet, Scripts, ScrollRestoration, useRouteLoaderData, LiveReload } from "@remix-run/react";
import { Footer } from "./components/Footer";
import { Navbar } from "./components/Navbar";
import Layout from "~/components/Layout";
import styles from "./tailwind.css";
import { getOptionalUser } from "./server/auth.server";
import { supabase } from '~/utils/supabase.server';

export const links: LinksFunction = () => [
  { rel: "stylesheet", href: styles },
];

export const loader = async ({ request,context }: LoaderFunctionArgs) => {
  const user = await getOptionalUser({ context });
  const { data: { session } } = await supabase.auth.getSession();
  
  return json({
    user,
    isAuthenticated: !!session,
    env: {
      SUPABASE_URL: process.env.SUPABASE_URL,
      SUPABASE_ANON_KEY: process.env.SUPABASE_ANON_KEY,
    }
  });
};

export const useOptionalUser = () => {
  const data = useRouteLoaderData<typeof loader>("root");

  if (!data) {
    throw new Error('Root loader was not run');
  }
  return data.user;
}

declare module "@remix-run/node" {
  interface AppLoadContext {
    remixService: RemixService; // Changed from 'any' to 'RemixService'
    user: unknown
  }
}

export default function App() {
  return (
    <html>
      <head>
        <meta charSet="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <Meta />
        <Links />
      </head>
      <body>
        <Layout>
          <Outlet />
        </Layout>
        <Scripts />
        <LiveReload />
      </body>
    </html>
  );
}