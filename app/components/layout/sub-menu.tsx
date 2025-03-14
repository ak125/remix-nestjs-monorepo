import { Link } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { useSubNav } from "~/hooks/use-sub-nav";

interface SubMenuLink {
  href: string;
  label: string;
}

export function SubMenu() {
  const { currentDepartment } = useSubNav();
  
  const links: SubMenuLink[] = [
    {
      href: `/${currentDepartment}`,
      label: "Accueil"
    },
    {
      href: `/${currentDepartment}/catalogue`,
      label: "Catalogue"
    },
    {
      href: `/${currentDepartment}/sitemap`,
      label: "Plan du site"
    }
  ];

  return (
    <nav className="w-full bg-muted py-2 hidden md:block border-b">
      <div className="container mx-auto">
        <ul className="flex justify-center items-center gap-6">
          {links.map(link => (
            <li key={link.href}>
              <Button 
                asChild
                variant="ghost" 
                className="text-muted-foreground hover:text-primary"
              >
                <Link to={link.href}>
                  {link.label}
                </Link>
              </Button>
            </li>
          ))}
        </ul>
      </div>
    </nav>
  );
}
