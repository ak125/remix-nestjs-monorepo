import { useMemo } from "react";
import type { BreadcrumbItem } from "~/components/ui/breadcrumb";

export function useBreadcrumbSchema(items: BreadcrumbItem[]) {
  return useMemo(() => ({
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    itemListElement: [
      {
        "@type": "ListItem",
        position: 1,
        name: "Accueil",
        item: typeof window !== 'undefined' ? `${window.location.origin}/` : '/'
      },
      ...items.map(item => ({
        "@type": "ListItem",
        position: item.position,
        name: item.name,
        ...(item.href && {
          item: typeof window !== 'undefined' 
            ? `${window.location.origin}${item.href}`
            : item.href
        })
      }))
    ]
  }), [items]);
}
