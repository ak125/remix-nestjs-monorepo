import { useEffect, useState } from 'react';
import { useFetcher } from '@remix-run/react';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "~/components/ui/select";
import { Label } from "~/components/ui/label";
import { Skeleton } from "~/components/ui/skeleton";

interface ModelSelectorProps {
  gammeId: string;
  marqueId: string;
  year: string | number;
  value?: string;
  onChange: (value: string) => void;
  label?: string;
  placeholder?: string;
}

interface ModeleOption {
  id: string;
  name: string;
  yearFrom: number;
  yearTo: number;
  typesCount: number;
}

export function ModelSelector({ 
  gammeId, 
  marqueId, 
  year, 
  value, 
  onChange,
  label = "Modèle",
  placeholder = "Sélectionner un modèle"
}: ModelSelectorProps) {
  const fetcher = useFetcher<ModeleOption[]>();
  
  // Charger les modèles lorsque les paramètres changent
  useEffect(() => {
    if (gammeId && marqueId && year) {
      fetcher.load(`/api/modele?gammeId=${gammeId}&marqueId=${marqueId}&year=${year}`);
    }
  }, [gammeId, marqueId, year, fetcher]);
  
  // États de chargement et modèles disponibles
  const isLoading = fetcher.state === 'loading';
  const modeles = fetcher.data || [];
  const hasModeles = modeles.length > 0;
  
  // Composant visuel
  return (
    <div className="space-y-2">
      <Label htmlFor="modele-selector">{label}</Label>
      
      {isLoading ? (
        <Skeleton className="h-10 w-full" />
      ) : (
        <Select
          disabled={!hasModeles}
          value={value}
          onValueChange={onChange}
        >
          <SelectTrigger id="modele-selector" className="w-full">
            <SelectValue placeholder={placeholder} />
          </SelectTrigger>
          <SelectContent>
            {hasModeles ? (
              modeles.map((modele) => (
                <SelectItem key={modele.id} value={modele.id}>
                  {modele.name} ({modele.yearFrom} - {modele.yearTo === new Date().getFullYear() ? "Présent" : modele.yearTo})
                </SelectItem>
              ))
            ) : (
              <SelectItem disabled value="empty">
                Aucun modèle disponible
              </SelectItem>
            )}
          </SelectContent>
        </Select>
      )}
      
      {!isLoading && !hasModeles && marqueId && (
        <p className="text-xs text-muted-foreground">
          Aucun modèle disponible pour cette sélection
        </p>
      )}
    </div>
  );
}
