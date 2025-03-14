import { useState, useEffect } from "react";
import { useFetcher, useNavigate } from "@remix-run/react";
import { useDebounce } from "~/hooks/use-debounce";
import { Input } from "~/components/ui/input";
import { Button } from "~/components/ui/button";
import { Search, Loader2 } from "lucide-react";
import { Command, CommandGroup, CommandItem } from "~/components/ui/command";

interface SearchResult {
  id: string;
  title: string;
  url: string;
}

export function QuickSearch() {
  const [query, setQuery] = useState("");
  const [isOpen, setIsOpen] = useState(false);
  const debouncedQuery = useDebounce(query);
  const fetcher = useFetcher<SearchResult[]>();
  const navigate = useNavigate();

  useEffect(() => {
    if (debouncedQuery.length >= 2) {
      fetcher.load(`/api/search?q=${encodeURIComponent(debouncedQuery)}`);
      setIsOpen(true);
    } else {
      setIsOpen(false);
    }
  }, [debouncedQuery]);

  return (
    <div className="relative w-full max-w-lg">
      <div className="flex gap-2">
        <Input
          type="search"
          placeholder="Rechercher une pièce..."
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          className="w-full"
        />
        <Button type="submit" size="icon">
          {fetcher.state === "loading" ? (
            <Loader2 className="h-4 w-4 animate-spin" />
          ) : (
            <Search className="h-4 w-4" />
          )}
        </Button>
      </div>

      {isOpen && fetcher.data && (
        <Command className="absolute top-full w-full mt-2 border shadow-md rounded-lg bg-popover">
          <CommandGroup>
            {fetcher.data.map((result) => (
              <CommandItem
                key={result.id}
                onSelect={() => {
                  navigate(result.url);
                  setQuery("");
                  setIsOpen(false);
                }}
              >
                <span>{result.title}</span>
              </CommandItem>
            ))}
          </CommandGroup>
        </Command>
      )}
    </div>
  );
}
