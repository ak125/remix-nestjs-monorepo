import { useNavigate } from "@remix-run/react";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "~/components/ui/select";
import { useFetcher } from "@remix-run/react";
import { useEffect } from "react";
import { Skeleton } from "~/components/ui/skeleton";

interface MotorisationSelectProps {
  marqueId?: number;
  modeleId?: number;
  year?: number;
  onSelect?: (motorId: string) => void;
}

export function MotorisationSelect({ marqueId, modeleId, year, onSelect }: MotorisationSelectProps) {
  const navigate = useNavigate();
  const fetcher = useFetcher();

  useEffect(() => {
    if (marqueId && modeleId && year) {
      fetcher.load(`/api/motorisations?marqueId=${marqueId}&modeleId=${modeleId}&year=${year}`);
    }
  }, [marqueId, modeleId, year]);

  const handleSelect = (value: string) => {
    if (onSelect) {
      onSelect(value);
    } else {
      navigate(value);
    }
  };

  const isLoading = fetcher.state === "loading";
  const groups = fetcher.data?.groups || [];

  return (
    <Select onValueChange={handleSelect} disabled={isLoading}>
      <SelectTrigger className="w-full">
        <SelectValue placeholder="Sélectionnez une motorisation" />
      </SelectTrigger>
      <SelectContent>
        {isLoading ? (
          <div className="p-2">
            <Skeleton className="h-4 w-full" />
            <Skeleton className="h-4 w-full mt-2" />
          </div>
        ) : groups.length > 0 ? (
          groups.map(group => (
            <SelectGroup key={group.fuel}>
              <SelectLabel>{group.fuel}</SelectLabel>
              {group.motors.map(motor => (
                <SelectItem 
                  key={motor.id} 
                  value={`/auto/${motor.alias}-${motor.id}`}
                >
                  {motor.name} - {motor.power} ({motor.years})
                </SelectItem>
              ))}
            </SelectGroup>
          ))
        ) : (
          <div className="p-2 text-sm text-muted-foreground">
            Aucune motorisation disponible
          </div>
        )}
      </SelectContent>
    </Select>
  );
}
