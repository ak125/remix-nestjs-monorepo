import { useEffect, useState, useRef } from 'react';
import { useFetcher } from '@remix-run/react';
import { useDebounce } from '~/utils/hooks';
import { motion, AnimatePresence } from 'framer-motion';

interface SearchResult {
  id: string;
  name: string;
  reference: string;
  alias: string;
  category: {
    name: string;
    alias: string;
  };
}

export function SearchBox() {
  const [query, setQuery] = useState('');
  const [isOpen, setIsOpen] = useState(false);
  const debouncedQuery = useDebounce(query, 300);
  const containerRef = useRef<HTMLDivElement>(null);
  const fetcher = useFetcher();

  useEffect(() => {
    if (debouncedQuery.length >= 2) {
      fetcher.load(`/api/search?q=${encodeURIComponent(debouncedQuery)}`);
    }
  }, [debouncedQuery]);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    }

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const results = (fetcher.data?.results || []) as SearchResult[];
  const showResults = isOpen && query.length >= 2;

  return (
    <div ref={containerRef} className="relative">
      <div className="relative">
        <input
          type="search"
          value={query}
          onChange={e => {
            setQuery(e.target.value);
            setIsOpen(true);
          }}
          onFocus={() => setIsOpen(true)}
          placeholder="Rechercher une pièce..."
          className="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        {fetcher.state === 'loading' && (
          <div className="absolute right-3 top-1/2 -translate-y-1/2">
            <svg className="animate-spin h-5 w-5 text-gray-400" viewBox="0 0 24 24">
              <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none" />
              <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
            </svg>
          </div>
        )}
      </div>

      <AnimatePresence>
        {showResults && (
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -10 }}
            className="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-lg overflow-hidden"
          >
            {results.length > 0 ? (
              <ul className="divide-y divide-gray-100">
                {results.map(result => (
                  <li key={result.id}>
                    <a
                      href={`/${result.category.alias}/${result.alias}`}
                      className="block px-4 py-3 hover:bg-gray-50"
                      onClick={() => setIsOpen(false)}
                    >
                      <div className="font-medium">{result.name}</div>
                      <div className="text-sm text-gray-500">
                        {result.reference} - {result.category.name}
                      </div>
                    </a>
                  </li>
                ))}
              </ul>
            ) : (
              <div className="px-4 py-3 text-sm text-gray-500">
                Aucun résultat trouvé
              </div>
            )}
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}
