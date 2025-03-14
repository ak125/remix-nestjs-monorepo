import { useEffect, useState } from "react";
import { useFetcher } from "@remix-run/react";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "~/components/ui/select";
import { Label } from "~/components/ui/label";
import { Skeleton } from "~/components/ui/skeleton";

interface YearSelectorProps {
  gammeId: string;
  marqueId: string;
  value?: string;
  onChange: (value: string) => void;
  label?: string;
  placeholder?: string;
}

interface YearOption {
  value: number;
  label: string;
  favorite: boolean;
}

export function YearSelector({
  gammeId,
  marqueId,
  value,
  onChange,
  label = "Année",
  placeholder = "Sélectionner une année"
}: YearSelectorProps) {
  const fetcher = useFetcher<YearOption[]>();
  
  // Charger les années lorsque les paramètres changent
  useEffect(() => {
    if (gammeId && marqueId) {
      fetcher.load(`/api/car-years?gammeId=${gammeId}&marqueId=${marqueId}`);
    }
  }, [gammeId, marqueId, fetcher]);
  
  // États de chargement et années disponibles
  const isLoading = fetcher.state === 'loading';
  const years = fetcher.data || [];
  const hasYears = years.length > 0;
  
  // Composant visuel
  return (
    <div className="space-y-2">
      <Label htmlFor="year-selector">{label}</Label>
      
      {isLoading ? (
        <Skeleton className="h-10 w-full" />
      ) : (
        <Select
          disabled={!hasYears}
          value={value}
          onValueChange={onChange}
        >
          <SelectTrigger id="year-selector" className="w-full">
            <SelectValue placeholder={placeholder} />
          </SelectTrigger>
          <SelectContent>
            {hasYears ? (
              years.map((year) => (
                <SelectItem 
                  key={year.value} 
                  value={year.value.toString()}
                  className={year.favorite ? "font-bold" : ""}
                >
                  {year.label} {year.favorite && "★"}
                </SelectItem>
              ))
            ) : (
              <SelectItem disabled value="empty">
                Aucune année disponible
              </SelectItem>
            )}
          </SelectContent>
        </Select>
      )}
      
      {!isLoading && !hasYears && marqueId && (
        <p className="text-xs text-muted-foreground">
          Aucune année disponible pour cette sélection
        </p>
      )}
    </div>
  );
}
