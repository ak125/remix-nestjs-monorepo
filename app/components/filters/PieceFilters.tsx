import { useFetcher } from "@remix-run/react";
import { useEffect, useState } from "react";
import { clsx } from "clsx";

interface Filter {
  id: string;
  name: string;
  count: number;
}

interface Props {
  initialFilters?: Filter[];
  onChange?: (filters: string[]) => void;
}

export function PieceFilters({ initialFilters = [], onChange }: Props) {
  const [selectedFilters, setSelectedFilters] = useState<string[]>([]);
  const fetcher = useFetcher();

  useEffect(() => {
    onChange?.(selectedFilters);
  }, [selectedFilters, onChange]);

  useEffect(() => {
    async function fetchFilters() {
      const response = await fetch('/api/filters');
      if (response.ok) {
        const data = await response.json();
        setSelectedFilters(data);
      }
    }

    if (!initialFilters.length) {
      fetchFilters();
    }
  }, [initialFilters]);

  return (
    <div className="space-y-4">
      <div className="font-medium">Filtres</div>

      <div className="space-y-2">
        {initialFilters.map((filter) => (
          <label 
            key={filter.id}
            className={clsx(
              "flex items-center gap-2 rounded-lg border p-2 cursor-pointer",
              selectedFilters.includes(filter.id) && "bg-blue-50 border-blue-200"
            )}
          >
            <input
              type="checkbox"
              className="rounded border-gray-300"
              checked={selectedFilters.includes(filter.id)}
              onChange={(e) => {
                if (e.target.checked) {
                  setSelectedFilters([...selectedFilters, filter.id]);
                } else {
                  setSelectedFilters(
                    selectedFilters.filter((id) => id !== filter.id)
                  );
                }
              }}
            />
            <span className="text-sm">
              {filter.name} ({filter.count})
            </span>
          </label>
        ))}
      </div>
    </div>
  );
}
