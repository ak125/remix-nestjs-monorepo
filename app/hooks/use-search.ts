import { useState, useEffect } from "react";
import { useFetcher } from "@remix-run/react";
import { useDebounce } from "~/hooks/use-debounce";

export function useSearch() {
  const [search, setSearch] = useState("");
  const [results, setResults] = useState<Array<{
    id: string;
    name: string;
    category: string;
    href: string;
  }>>([]);

  const debouncedSearch = useDebounce(search, 300);
  const fetcher = useFetcher();

  useEffect(() => {
    if (debouncedSearch.length < 2) {
      setResults([]);
      return;
    }

    fetcher.load(`/api/search?q=${encodeURIComponent(debouncedSearch)}`);
  }, [debouncedSearch, fetcher]);

  useEffect(() => {
    if (fetcher.data) {
      setResults(fetcher.data.results);
    }
  }, [fetcher.data]);

  return { search, setSearch, results, isLoading: fetcher.state === "loading" };
}
