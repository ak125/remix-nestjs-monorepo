import { Outlet } from "@remix-run/react";
import { TopMenu } from "~/components/layout/top-menu";
import { MainNav } from "~/components/layout/main-nav";
import { FooterModern } from "~/components/layout/footer-modern";

export default function Layout() {
  return (
    <div className="flex flex-col min-h-screen">
      <header>
        <TopMenu />
        <MainNav />
      </header>
      
      <main className="flex-grow">
        <Outlet />
      </main>
      
      <FooterModern />
    </div>
  );
}
