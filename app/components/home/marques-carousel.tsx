import { useState, useEffect, useRef } from "react";
import { Link } from "@remix-run/react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { Button } from "~/components/ui/button";
import { cn } from "~/lib/utils";

interface MarqueCarouselProps {
  marques: Array<{
    id: number;
    name: string;
    nameMeta: string;
    logo: string;
    alias: string;
    top?: boolean;
  }>;
  title: string;
  className?: string;
}

export function MarquesCarousel({ marques, title, className }: MarqueCarouselProps) {
  const carouselRef = useRef<HTMLDivElement>(null);
  const [scrollPosition, setScrollPosition] = useState(0);
  const [maxScroll, setMaxScroll] = useState(0);

  useEffect(() => {
    if (carouselRef.current) {
      setMaxScroll(carouselRef.current.scrollWidth - carouselRef.current.clientWidth);
    }

    const handleResize = () => {
      if (carouselRef.current) {
        setMaxScroll(carouselRef.current.scrollWidth - carouselRef.current.clientWidth);
      }
    };

    window.addEventListener("resize", handleResize);
    return () => window.removeEventListener("resize", handleResize);
  }, [marques]);

  const scrollLeft = () => {
    if (carouselRef.current) {
      const newPosition = Math.max(0, scrollPosition - carouselRef.current.clientWidth / 2);
      carouselRef.current.scrollTo({ left: newPosition, behavior: "smooth" });
      setScrollPosition(newPosition);
    }
  };

  const scrollRight = () => {
    if (carouselRef.current) {
      const newPosition = Math.min(
        maxScroll,
        scrollPosition + carouselRef.current.clientWidth / 2
      );
      carouselRef.current.scrollTo({ left: newPosition, behavior: "smooth" });
      setScrollPosition(newPosition);
    }
  };

  const handleScroll = () => {
    if (carouselRef.current) {
      setScrollPosition(carouselRef.current.scrollLeft);
    }
  };

  return (
    <div className={cn("relative", className)}>
      <h2 className="text-2xl font-bold mb-6">{title}</h2>
      <div className="flex items-center">
        <Button
          variant="outline"
          size="icon"
          className={cn(
            "absolute left-0 z-10 rounded-full bg-white shadow-md border-gray-200",
            scrollPosition <= 0 ? "opacity-50 cursor-not-allowed" : "opacity-100"
          )}
          onClick={scrollLeft}
          disabled={scrollPosition <= 0}
        >
          <ChevronLeft className="h-4 w-4" />
          <span className="sr-only">Précédent</span>
        </Button>

        <div
          ref={carouselRef}
          className="flex overflow-x-auto gap-4 py-4 px-2 scroll-smooth no-scrollbar"
          onScroll={handleScroll}
        >
          {marques.map((marque) => (
            <Link
              key={marque.id}
              to={`/auto/${marque.alias}-${marque.id}.html`}
              className="flex-shrink-0 w-32 p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow flex flex-col items-center justify-center"
            >
              <img
                src={`/upload/constructeurs-automobiles/marques-logos/${marque.logo}`}
                alt={marque.nameMeta}
                title={marque.nameMeta}
                className="h-10 w-auto object-contain"
                loading="lazy"
              />
              <p className="text-xs text-center mt-2 font-medium truncate w-full">
                {marque.name}
              </p>
            </Link>
          ))}
        </div>

        <Button
          variant="outline"
          size="icon"
          className={cn(
            "absolute right-0 z-10 rounded-full bg-white shadow-md border-gray-200",
            scrollPosition >= maxScroll ? "opacity-50 cursor-not-allowed" : "opacity-100"
          )}
          onClick={scrollRight}
          disabled={scrollPosition >= maxScroll}
        >
          <ChevronRight className="h-4 w-4" />
          <span className="sr-only">Suivant</span>
        </Button>
      </div>
    </div>
  );
}
