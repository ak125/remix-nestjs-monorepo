import { useState, useEffect, useRef } from "react";
import { useNavigate, useFetcher } from "@remix-run/react";
import { useDebounce } from "~/hooks/use-debounce";
import { cn } from "~/lib/utils";
import { 
  Search as SearchIcon, 
  X as ClearIcon, 
  Loader2, 
  Tag, 
  Tag as CategoryIcon, 
  Box,
  ArrowRight
} from "lucide-react";
import { Input } from "~/components/ui/input";
import { Button } from "~/components/ui/button";

interface SearchResult {
  id: string | number;
  name: string;
  type: 'product' | 'category' | 'brand';
  url: string;
  catalogNumber?: string;
}

interface QuickSearchProps {
  onDismiss?: () => void;
  autoFocus?: boolean;
  className?: string;
}

export function QuickSearch({ onDismiss, autoFocus = false, className }: QuickSearchProps) {
  const navigate = useNavigate();
  const fetcher = useFetcher<{ results: SearchResult[] }>();
  const [query, setQuery] = useState("");
  const debouncedQuery = useDebounce(query, 300);
  const [showResults, setShowResults] = useState(false);
  const resultsRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);
  
  // Handle search input
  const handleSearch = (event: React.ChangeEvent<HTMLInputElement>) => {
    setQuery(event.target.value);
  };
  
  // Handle clearing the search
  const handleClear = () => {
    setQuery("");
    if (inputRef.current) {
      inputRef.current.focus();
    }
  };
  
  // Handle submitting the search form
  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault();
    if (query.trim()) {
      navigate(`/recherche?q=${encodeURIComponent(query)}`);
      if (onDismiss) onDismiss();
    }
  };
  
  // Handle clicking a search result
  const handleResultClick = (result: SearchResult) => {
    navigate(result.url);
    setShowResults(false);
    if (onDismiss) onDismiss();
  };
  
  // Close results when clicking outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (
        resultsRef.current && 
        !resultsRef.current.contains(event.target as Node) && 
        inputRef.current && 
        !inputRef.current.contains(event.target as Node)
      ) {
        setShowResults(false);
      }
    };
    
    document.addEventListener("mousedown", handleClickOutside);
    return () => {
      document.removeEventListener("mousedown", handleClickOutside);
    };
  }, []);
  
  // Load search results when query changes
  useEffect(() => {
    if (debouncedQuery.length >= 2) {
      fetcher.load(`/api/search?q=${encodeURIComponent(debouncedQuery)}`);
      setShowResults(true);
    } else {
      setShowResults(false);
    }
  }, [debouncedQuery]);
  
  // Auto focus if requested
  useEffect(() => {
    if (autoFocus && inputRef.current) {
      inputRef.current.focus();
    }
  }, [autoFocus]);
  
  // Get search results data
  const results = fetcher.data?.results || [];
  const isLoading = fetcher.state === "loading";

  return (
    <div className={cn("relative w-full max-w-lg", className)}>
      <form onSubmit={handleSubmit} className="relative">
        <Input
          ref={inputRef}
          type="search"
          value={query}
          onChange={handleSearch}
          placeholder="Rechercher un produit, une catégorie..."
          className="pr-12"
        />
        <div className="absolute inset-y-0 right-0 flex items-center">
          {query ? (
            <Button 
              type="button" 
              variant="ghost" 
              size="sm" 
              className="h-full px-2 text-muted-foreground" 
              onClick={handleClear}
            >
              <ClearIcon className="h-4 w-4" />
              <span className="sr-only">Effacer</span>
            </Button>
          ) : (
            <div className="pr-3">
              {isLoading ? (
                <Loader2 className="h-4 w-4 animate-spin text-muted-foreground" />
              ) : (
                <SearchIcon className="h-4 w-4 text-muted-foreground" />
              )}
            </div>
          )}
        </div>
      </form>
      
      {/* Results dropdown */}
      {showResults && (
        <div 
          ref={resultsRef}
          className="absolute left-0 right-0 top-full z-10 mt-1 max-h-[70vh] overflow-y-auto rounded-md border bg-background shadow-lg"
        >
          {results.length > 0 ? (
            <>
              <ul className="p-2 divide-y">
                {results.map((result) => (
                  <li key={`${result.type}-${result.id}`}>
                    <button
                      onClick={() => handleResultClick(result)}
                      className="flex w-full items-center px-3 py-2 text-left text-sm hover:bg-accent rounded-sm"
                    >
                      {/* Icon based on result type */}
                      <span className="mr-3 text-muted-foreground">
                        {result.type === 'product' && <Box className="h-4 w-4" />}
                        {result.type === 'category' && <CategoryIcon className="h-4 w-4" />}
                        {result.type === 'brand' && <Tag className="h-4 w-4" />}
                      </span>
                      
                      <div className="flex-1">
                        <p className="font-medium">{result.name}</p>
                        {result.catalogNumber && (
                          <p className="text-xs text-muted-foreground">
                            {result.catalogNumber}
                          </p>
                        )}
                      </div>
                      
                      <ArrowRight className="h-4 w-4 text-muted-foreground" />
                    </button>
                  </li>
                ))}
              </ul>
              
              <div className="p-2 border-t">
                <Button 
                  variant="ghost" 
                  className="w-full justify-between" 
                  onClick={() => {
                    navigate(`/recherche?q=${encodeURIComponent(query)}`);
                    if (onDismiss) onDismiss();
                  }}
                >
                  <span>Voir tous les résultats</span>
                  <ArrowRight className="h-4 w-4 ml-2" />
                </Button>
              </div>
            </>
          ) : (
            <div className="p-6 text-center text-muted-foreground">
              {isLoading ? (
                <div className="flex flex-col items-center">
                  <Loader2 className="h-8 w-8 animate-spin mb-2" />
                  <p>Recherche en cours...</p>
                </div>
              ) : (
                <>
                  <p className="mb-2">Aucun résultat pour "{query}"</p>
                  <p className="text-sm">
                    Essayez avec un autre terme ou parcourez nos catégories
                  </p>
                </>
              )}
            </div>
          )}
        </div>
      )}
    </div>
  );
}
