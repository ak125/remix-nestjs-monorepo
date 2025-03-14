import { json, LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData, Form } from "@remix-run/react";
import { prisma } from "~/lib/prisma.server";
import { Select } from "~/components/ui/select";
import { Button } from "~/components/ui/button";
import { Loader2 } from "lucide-react";

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const params = {
    marqueId: Number(url.searchParams.get("marqueId")),
    year: Number(url.searchParams.get("year")),
    modeleId: Number(url.searchParams.get("modeleId"))
  };

  if (!params.marqueId || !params.year || !params.modeleId) {
    return json({ motorisations: [] });
  }

  try {
    const motorisations = await prisma.autoType.findMany({
      where: {
        TYPE_MODELE_ID: params.modeleId,
        TYPE_DISPLAY: true,
        AND: [
          { TYPE_YEAR_FROM: { lte: params.year } },
          { 
            OR: [
              { TYPE_YEAR_TO: { gte: params.year } },
              { TYPE_YEAR_TO: null }
            ]
          }
        ]
      },
      include: {
        AUTO_MODELE: {
          include: {
            AUTO_MARQUE: true
          }
        }
      },
      orderBy: [
        { TYPE_NAME: 'asc' },
        { TYPE_POWER_PS: 'asc' }
      ]
    });

    return json({ 
      motorisations,
      error: null
    }, {
      headers: {
        'Cache-Control': 'public, max-age=300'
      }
    });

  } catch (error) {
    console.error('Failed to load motorisations:', error);
    return json({ 
      motorisations: [],
      error: "Impossible de charger les motorisations"
    });
  }
}

export default function MotorisationsPage() {
  const { motorisations, error } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-md mx-auto">
        <h1 className="text-2xl font-bold mb-6">
          Sélectionner une motorisation
        </h1>

        {error ? (
          <div className="p-4 border border-red-200 bg-red-50 text-red-600 rounded-lg">
            {error}
          </div>
        ) : (
          <Form method="get" className="space-y-4">
            <Select.Root name="motorisation">
              <Select.Trigger className="w-full">
                <Select.Value placeholder="Choisissez une motorisation" />
              </Select.Trigger>
              <Select.Content>
                {motorisations.map(moto => (
                  <Select.Item 
                    key={moto.TYPE_ID}
                    value={`/searchcar/${moto.AUTO_MODELE.AUTO_MARQUE.MARQUE_ALIAS}-${moto.AUTO_MODELE.AUTO_MARQUE.MARQUE_ID}/${moto.AUTO_MODELE.MODELE_ALIAS}-${moto.AUTO_MODELE.MODELE_ID}/${moto.TYPE_ALIAS}-${moto.TYPE_ID}`}
                  >
                    {moto.TYPE_NAME} {moto.TYPE_FUEL} {moto.TYPE_POWER_PS} Ch
                  </Select.Item>
                ))}
              </Select.Content>
            </Select.Root>

            <Button type="submit" className="w-full">
              Valider
            </Button>
          </Form>
        )}
      </div>
    </div>
  );
}
