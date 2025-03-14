import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "~/components/ui/table";

interface CompareTableProps {
  models: Array<{
    id: number;
    name: string;
    marque: { name: string };
    specifications: {
      engine: string;
      horsepower: number;
      fuelType: string;
      consumption: number;
      emissions: number;
      acceleration: number;
      maxSpeed: number;
      weight: number;
      dimensions: {
        length: number;
        width: number;
        height: number;
      };
    };
  }>;
}

export function CompareTable({ models }: CompareTableProps) {
  const specs = [
    { key: 'engine', label: 'Moteur' },
    { key: 'horsepower', label: 'Puissance (ch)' },
    { key: 'fuelType', label: 'Carburant' },
    { key: 'consumption', label: 'Conso. (L/100km)' },
    { key: 'emissions', label: 'Émissions CO2 (g/km)' },
    { key: 'acceleration', label: '0-100 km/h (s)' },
    { key: 'maxSpeed', label: 'Vitesse max (km/h)' },
    { key: 'weight', label: 'Poids (kg)' }
  ];

  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Spécifications</TableHead>
          {models.map(model => (
            <TableHead key={model.id}>
              {model.marque.name} {model.name}
            </TableHead>
          ))}
        </TableRow>
      </TableHeader>
      <TableBody>
        {specs.map(spec => (
          <TableRow key={spec.key}>
            <TableCell>{spec.label}</TableCell>
            {models.map(model => (
              <TableCell key={`${model.id}-${spec.key}`}>
                {model.specifications[spec.key]}
              </TableCell>
            ))}
          </TableRow>
        ))}
      </TableBody>
    </Table>
  );
}
