type MetaTagsOptions = {
  title: string;
  description: string;
  keywords: string;
  image?: string;
  canonicalUrl?: string;
  noindex?: boolean;
};

export function generateMetaTags({
  title,
  description,
  keywords,
  image,
  canonicalUrl,
  noindex = false,
}: MetaTagsOptions) {
  const metaTags: Record<string, string> = {
    title,
    description,
    keywords,
    "robots": noindex ? "noindex, nofollow" : "index, follow",
    "og:title": title,
    "og:description": description,
    "og:type": "website",
    "twitter:card": "summary_large_image",
    "twitter:title": title,
    "twitter:description": description,
  };

  if (image) {
    metaTags["og:image"] = image;
    metaTags["twitter:image"] = image;
  }

  if (canonicalUrl) {
    metaTags["canonical"] = canonicalUrl;
  }

  return metaTags;
}

// Fonction utilitaire pour uniformiser le format des URLs
export function formatUrlForSeo(url: string): string {
  // Supprime les doubles slashes et les slashes à la fin
  return url.replace(/([^:]\/)\/+/g, "$1").replace(/\/+$/, "");
}
