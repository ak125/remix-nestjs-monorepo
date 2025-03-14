import { json, LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Select } from "~/components/ui/select";
import { Button } from "~/components/ui/button";
import { prisma } from "~/lib/prisma.server";

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const params = {
    marqueId: Number(url.searchParams.get("marqueId")) || undefined,
    modeleId: Number(url.searchParams.get("modeleId")) || undefined,
    typeId: Number(url.searchParams.get("typeId")) || undefined
  };

  try {
    const carType = await prisma.autoType.findFirst({
      where: {
        TYPE_ID: params.typeId,
        TYPE_DISPLAY: true,
        AUTO_MODELE: {
          MODELE_ID: params.modeleId,
          MODELE_DISPLAY: true,
          AUTO_MARQUE: {
            MARQUE_ID: params.marqueId,
            MARQUE_DISPLAY: true
          }
        }
      },
      include: {
        AUTO_MODELE: {
          include: {
            AUTO_MARQUE: true
          }
        }
      }
    });

    return json({
      carType,
      error: null
    }, {
      headers: {
        'Cache-Control': 'public, max-age=300'
      }
    });

  } catch (error) {
    console.error('Failed to load car type:', error);
    return json({ 
      carType: null,
      error: "Impossible de charger les données" 
    });
  }
}

export default function CarTypePage() {
  const { carType, error } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-xl mx-auto space-y-6">
        <h1 className="text-2xl font-bold">
          {carType ? (
            `${carType.AUTO_MODELE.AUTO_MARQUE.MARQUE_NAME} ${carType.AUTO_MODELE.MODELE_NAME}`
          ) : (
            'Type de véhicule'
          )}
        </h1>

        {error ? (
          <div className="p-4 border border-red-200 bg-red-50 text-red-600 rounded-lg">
            {error}
          </div>
        ) : carType && (
          <div className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="text-sm text-gray-500">Motorisation</label>
                <p className="font-medium">{carType.TYPE_NAME}</p>
              </div>
              
              <div>
                <label className="text-sm text-gray-500">Puissance</label>
                <p className="font-medium">{carType.TYPE_POWER_PS} ch</p>
              </div>

              <div>
                <label className="text-sm text-gray-500">Carburant</label>
                <p className="font-medium">{carType.TYPE_FUEL}</p>
              </div>

              <div>
                <label className="text-sm text-gray-500">Années</label>
                <p className="font-medium">
                  {carType.TYPE_YEAR_FROM} 
                  {carType.TYPE_YEAR_TO && ` - ${carType.TYPE_YEAR_TO}`}
                </p>
              </div>
            </div>

            <Button className="w-full">
              Voir les pièces disponibles
            </Button>
          </div>
        )}
      </div>
    </div>
  );
}
