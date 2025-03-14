import { useFetcher } from "@remix-run/react";
import { useEffect, useState, useRef } from "react";
import debounce from "lodash/debounce";
import { useUser } from "~/utils/user";

interface ModelSelectProps {
  marqueId: string;
  year: string;
  onSelect: (modelId: string) => void;
}

interface Model {
  id: number;
  name: string;
  yearFrom: number;
  yearTo: number | null;
  category?: string;
}

const CATEGORIES = [
  { value: "", label: "Toutes" },
  { value: "SUV", label: "🚙 SUV" },
  { value: "BERLINE", label: "🚗 Berline" },
  { value: "SPORT", label: "🏎️ Sportive" }
];

const STORAGE_KEY = "carFavorites";

export default function ModelSelect({
  marqueId,
  year,
  onSelect
}: ModelSelectProps) {
  const user = useUser();
  const modelsFetcher = useFetcher();
  const favoritesFetcher = useFetcher();
  const [search, setSearch] = useState("");
  const [category, setCategory] = useState("");
  const [loading, setLoading] = useState(false);
  const [favorites, setFavorites] = useState<Model[]>([]);
  const [showFavorites, setShowFavorites] = useState(false);

  // Load favorites from localStorage
  useEffect(() => {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored) {
      setFavorites(JSON.parse(stored));
    }
  }, []);

  const debouncedSearch = useRef(
    debounce((query: string, cat: string) => {
      const params = new URLSearchParams({
        formCarMarqueid: marqueId,
        formCarMarqueYear: year,
        search: query,
        category: cat
      });
      modelsFetcher.load(`/api/models?${params}`);
    }, 300)
  ).current;

  useEffect(() => {
    if (marqueId && year) {
      setLoading(true);
      debouncedSearch(search, category);
    }
  }, [marqueId, year, search, category]);

  useEffect(() => {
    if (modelsFetcher.data) {
      setLoading(false);
    }
  }, [modelsFetcher.data]);

  const toggleFavorite = async (modelId: number) => {
    if (!user) return;

    const isFavorite = favoritesFetcher.data?.favorites?.some(
      (f: any) => f.modelId === modelId
    );

    favoritesFetcher.submit(
      { modelId, action: isFavorite ? "remove" : "add" },
      { method: "post", action: "/api/favorites" }
    );
  };

  // Track model view
  const trackView = (modelId: number) => {
    modelsFetcher.submit(
      { modelId },
      { method: "post", action: "/api/models/view" }
    );
  };

  const models = showFavorites ? favorites : (modelsFetcher.data?.models || []);

  return (
    <div className="space-y-2">
      <div className="flex gap-2">
        <input
          type="text"
          className="w-full p-2 border rounded"
          placeholder="Rechercher un modèle..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          disabled={showFavorites}
        />

        <select 
          className="p-2 border rounded"
          value={category}
          onChange={(e) => setCategory(e.target.value)}
          disabled={showFavorites}
        >
          {CATEGORIES.map(cat => (
            <option key={cat.value} value={cat.value}>
              {cat.label}
            </option>
          ))}
        </select>

        <button
          onClick={() => setShowFavorites(!showFavorites)}
          className={`p-2 rounded ${
            showFavorites ? "bg-yellow-500 text-white" : "bg-gray-200"
          }`}
        >
          ⭐ Favoris
        </button>
      </div>

      <select
        onChange={(e) => {
          onSelect(e.target.value);
          trackView(Number(e.target.value));
        }}
        className="w-full p-2 border rounded"
        disabled={loading}
      >
        <option value="">- Modèle -</option>
        {models.map((model: Model) => {
          const isFavorite = favoritesFetcher.data?.favorites?.some(
            (f: any) => f.modelId === model.id
          );
          
          return (
            <option key={model.id} value={model.id}>
              {isFavorite ? "⭐ " : ""}{model.name} ({model.yearFrom}-{model.yearTo || 'présent'})
            </option>
          );
        })}
      </select>

      {loading && <div className="text-sm text-gray-500">Chargement...</div>}
    </div>
  );
}
