import { useNavigate } from "@remix-run/react";
import { 
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue 
} from "~/components/ui/select";
import { Input } from "~/components/ui/input";
import { Button } from "~/components/ui/button";
import { useEffect, useState } from "react";
import { useDebounce } from "~/hooks/useDebounce";

interface SearchFiltersProps {
  defaultValues?: {
    marque?: string;
    category?: string;
    prixMin?: string;
    prixMax?: string;
  };
}

export function SearchFilters({ defaultValues = {} }: SearchFiltersProps) {
  const navigate = useNavigate();
  const [filters, setFilters] = useState(defaultValues);
  const debouncedFilters = useDebounce(filters, 500);

  useEffect(() => {
    const queryParams = new URLSearchParams();
    Object.entries(debouncedFilters).forEach(([key, value]) => {
      if (value) queryParams.set(key, value);
    });
    
    navigate(`/search?${queryParams.toString()}`, { replace: true });
  }, [debouncedFilters, navigate]);

  return (
    <div className="flex flex-col gap-4 p-4 border rounded-lg bg-background/95 backdrop-blur">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <Select 
          name="marque"
          value={filters.marque}
          onValueChange={(value) => setFilters(f => ({ ...f, marque: value }))}
        >
          <SelectTrigger>
            <SelectValue placeholder="Marque" />
          </SelectTrigger>
          <SelectContent>
            {/* Liste des marques à charger dynamiquement */}
            <SelectItem value="tesla">Tesla</SelectItem>
            <SelectItem value="bmw">BMW</SelectItem>
          </SelectContent>
        </Select>

        <Input
          type="number"
          placeholder="Prix minimum"
          value={filters.prixMin || ''}
          onChange={e => setFilters(f => ({ ...f, prixMin: e.target.value }))}
        />

        <Input
          type="number"
          placeholder="Prix maximum"
          value={filters.prixMax || ''}
          onChange={e => setFilters(f => ({ ...f, prixMax: e.target.value }))}
        />

        <Button 
          variant="outline"
          onClick={() => setFilters({})}
        >
          Réinitialiser
        </Button>
      </div>
    </div>
  );
}
