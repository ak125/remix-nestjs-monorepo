import { useLocation } from "@remix-run/react";
import { useMemo } from "react";

interface BreadcrumbConfig {
  [key: string]: {
    name: string;
    parent?: string;
  };
}

const pathConfig: BreadcrumbConfig = {
  catalogue: { name: "Catalogue" },
  pieces: { name: "Pièces", parent: "catalogue" },
  marques: { name: "Marques", parent: "catalogue" },
  stock: { name: "Stock" },
  commandes: { name: "Commandes" }
};

export function useBreadcrumbs() {
  const location = useLocation();

  const breadcrumbs = useMemo(() => {
    const segments = location.pathname.split("/").filter(Boolean);
    const items = [];
    let currentPath = "";
    let position = 2; // Home is 1

    for (const segment of segments) {
      currentPath += `/${segment}`;
      const config = pathConfig[segment];

      if (config) {
        items.push({
          name: config.name,
          href: items.length === segments.length - 1 ? undefined : currentPath,
          position: position++
        });
      }
    }

    return items;
  }, [location]);

  return breadcrumbs;
}
