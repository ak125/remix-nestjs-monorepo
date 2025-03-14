import { useFetcher } from "@remix-run/react";
import { useEffect } from "react";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "~/components/ui/select";
import { Card } from "~/components/ui/card";
import { Skeleton } from "~/components/ui/skeleton";

interface ModelSelectorProps {
  marqueId?: number;
  year?: number;
  gammeId?: number;
  onSelect?: (modelId: string) => void;
}

export function ModelSelector({ marqueId, year, gammeId, onSelect }: ModelSelectorProps) {
  const fetcher = useFetcher();

  useEffect(() => {
    if (marqueId && year && gammeId) {
      fetcher.load(`/api/models?marqueId=${marqueId}&year=${year}&gammeId=${gammeId}`);
    }
  }, [marqueId, year, gammeId]);

  const isLoading = fetcher.state === "loading";
  const models = fetcher.data?.models || [];

  if (!marqueId || !year || !gammeId) {
    return null;
  }

  return (
    <Card className="p-4">
      <Select onValueChange={onSelect} disabled={isLoading}>
        <SelectTrigger>
          <SelectValue placeholder="Sélectionnez un modèle" />
        </SelectTrigger>
        
        <SelectContent>
          {isLoading ? (
            <div className="p-2">
              <Skeleton className="h-4 w-full" />
              <Skeleton className="h-4 w-full mt-2" />
              <Skeleton className="h-4 w-full mt-2" />
            </div>
          ) : models.length > 0 ? (
            models.map((model) => (
              <SelectItem key={model.id} value={String(model.id)}>
                {model.name}
                {model.specifications && (
                  <span className="text-sm text-muted-foreground ml-2">
                    {model.specifications.engine} - {model.specifications.horsepower}ch
                  </span>
                )}
              </SelectItem>
            ))
          ) : (
            <div className="p-2 text-sm text-muted-foreground">
              Aucun modèle disponible
            </div>
          )}
        </SelectContent>
      </Select>
    </Card>
  );
}
