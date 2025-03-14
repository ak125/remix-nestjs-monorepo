import { useState } from "react";
import { useLazyLoad } from "~/hooks/useLazyLoad";
import { cn } from "~/lib/utils";

interface LazyImageProps extends React.ImgHTMLAttributes<HTMLImageElement> {
  src: string;
  fallback?: string;
  blur?: boolean;
}

export function LazyImage({ 
  src, 
  alt, 
  className, 
  fallback = "/images/placeholder.jpg", 
  blur = true,
  ...props 
}: LazyImageProps) {
  const [isLoaded, setIsLoaded] = useState(false);
  
  const imgRef = useLazyLoad({
    onLoad: () => setIsLoaded(true)
  });

  return (
    <div className="relative overflow-hidden">
      <img
        ref={imgRef}
        data-src={src}
        src={fallback}
        alt={alt}
        className={cn(
          "transition-all duration-500",
          blur && !isLoaded && "scale-110 blur-xl",
          isLoaded && "scale-100 blur-0",
          className
        )}
        {...props}
      />
      {!isLoaded && (
        <div className="absolute inset-0 bg-gray-100 animate-pulse" />
      )}
    </div>
  );
}
