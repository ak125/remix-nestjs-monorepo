import { useLocation } from "@remix-run/react";
import { useEffect } from "react";

interface GoogleAnalyticsProps {
  trackingId?: string;
}

export function GoogleAnalytics({ trackingId = "UA-61260799-1" }: GoogleAnalyticsProps) {
  const location = useLocation();

  useEffect(() => {
    // Skip tracking in development
    if (process.env.NODE_ENV === 'development') return;

    // Initialize GA if not already initialized
    if (!window.gtag) {
      window.dataLayer = window.dataLayer || [];
      window.gtag = function() { window.dataLayer.push(arguments); };
      window.gtag('js', new Date());
      window.gtag('config', trackingId, { page_path: location.pathname });
    }
    
    // Track page view on route changes
    window.gtag('config', trackingId, { page_path: location.pathname + location.search });
  }, [location, trackingId]);

  if (process.env.NODE_ENV === 'development') {
    return null;
  }

  return (
    <>
      <script
        async
        src={`https://www.googletagmanager.com/gtag/js?id=${trackingId}`}
      />
      <script
        async
        id="gtag-init"
        dangerouslySetInnerHTML={{
          __html: `
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '${trackingId}', {
              page_path: window.location.pathname,
            });
          `,
        }}
      />
    </>
  );
}
