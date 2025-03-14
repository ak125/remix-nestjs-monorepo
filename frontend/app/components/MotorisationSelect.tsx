import { useFetcher } from "@remix-run/react";
import { useEffect, useState } from "react";

interface MotorisationProps {
  gammeId: string;
  marqueId: string;
  year: string;
  modelId: string;
  onSelect: (typeId: string) => void;
}

export default function MotorisationSelect({ 
  gammeId,
  marqueId, 
  year,
  modelId,
  onSelect
}: MotorisationProps) {
  const fetcher = useFetcher();
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (gammeId && marqueId && year && modelId) {
      setLoading(true);
      const params = new URLSearchParams({
        formGammeid: gammeId,
        formCarMarqueid: marqueId,
        formCarMarqueYear: year,
        formCarModelid: modelId
      });
      
      fetcher.load(`/motorisation?${params}`);
    }
  }, [gammeId, marqueId, year, modelId]);

  useEffect(() => {
    if (fetcher.data) {
      setLoading(false);
    }
  }, [fetcher.data]);

  const motorisations = fetcher.data?.types || [];
  const motors = motorisations.reduce((acc, m) => {
    const key = m.motor_type;
    if (!acc[key]) {
      acc[key] = [];
    }
    acc[key].push(m);
    return acc;
  }, {});

  if (loading) return <div>Chargement...</div>;

  return (
    <select 
      onChange={(e) => onSelect(e.target.value)}
      className="w-full p-2 border rounded"
      disabled={motorisations.length === 0}
    >
      <option value="">Sélectionner une motorisation</option>
      {Object.entries(motors).map(([motor, types]) => (
        <optgroup key={motor} label={motor}>
          {types.map((type) => (
            <option 
              key={type.type_id} 
              value={type.type_id}
            >
              {type.type_name} {type.type_fuel} {type.type_power_ps}ch 
              ({type.type_year_from}-{type.type_year_to || 'présent'})
            </option>
          ))}
        </optgroup>
      ))}
    </select>
  );
}
