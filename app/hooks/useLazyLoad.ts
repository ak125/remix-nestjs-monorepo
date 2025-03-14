import { useEffect, useRef } from "react";

interface UseLazyLoadOptions {
  rootMargin?: string;
  threshold?: number;
  onLoad?: () => void;
}

export function useLazyLoad(options: UseLazyLoadOptions = {}) {
  const imgRef = useRef<HTMLImageElement>(null);
  
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting && entry.target instanceof HTMLImageElement) {
            const img = entry.target;
            const src = img.dataset.src;
            
            if (src) {
              const newImage = new Image();
              newImage.onload = () => {
                img.src = src;
                img.classList.remove('opacity-0');
                options.onLoad?.();
              };
              newImage.src = src;
              observer.unobserve(img);
            }
          }
        });
      },
      {
        rootMargin: options.rootMargin || '50px',
        threshold: options.threshold || 0
      }
    );

    if (imgRef.current) {
      observer.observe(imgRef.current);
    }

    return () => observer.disconnect();
  }, [options.rootMargin, options.threshold, options.onLoad]);

  return imgRef;
}
