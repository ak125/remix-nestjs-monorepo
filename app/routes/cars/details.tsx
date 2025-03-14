import { json, LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { prisma } from "~/lib/prisma.server";

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const params = {
    marqueId: Number(url.searchParams.get("marqueId")),
    modeleId: Number(url.searchParams.get("modeleId")), 
    typeId: Number(url.searchParams.get("typeId"))
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

    if (!carType) {
      throw new Error('Car not found');
    }

    const meta = {
      title: `${carType.AUTO_MODELE.AUTO_MARQUE.MARQUE_NAME} ${carType.AUTO_MODELE.MODELE_NAME} ${carType.TYPE_NAME}`,
      description: `Catalogue pièces détachées pour ${carType.AUTO_MODELE.AUTO_MARQUE.MARQUE_NAME_META} ${carType.AUTO_MODELE.MODELE_NAME} ${carType.TYPE_NAME} ${carType.TYPE_POWER_PS} ch`
    };

    return json({ carType, meta }, {
      headers: {
        'Cache-Control': 'public, max-age=300'
      }
    });

  } catch (error) {
    throw new Response('Car details not found', { status: 404 });
  }
}

export default function CarDetailsPage() {
  const { carType, meta } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-2xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div className="p-6">
          <h1 className="text-2xl font-bold mb-4">
            {meta.title}
          </h1>

          <div className="grid grid-cols-2 gap-4 mb-6">
            <div className="space-y-2">
              <label className="text-sm text-gray-500">Motorisation</label>
              <p>{carType.TYPE_FUEL} - {carType.TYPE_POWER_PS} ch</p>
            </div>

            <div className="space-y-2">
              <label className="text-sm text-gray-500">Période</label>
              <p>{carType.TYPE_YEAR_FROM} - {carType.TYPE_YEAR_TO || 'Aujourd\'hui'}</p>
            </div>
          </div>

          <a 
            href={`/pieces/${carType.AUTO_MODELE.AUTO_MARQUE.MARQUE_ALIAS}-${carType.AUTO_MODELE.AUTO_MARQUE.MARQUE_ID}/${carType.AUTO_MODELE.MODELE_ALIAS}-${carType.AUTO_MODELE.MODELE_ID}/${carType.TYPE_ALIAS}-${carType.TYPE_ID}`}
            className="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Voir les pièces compatibles
          </a>
        </div>
      </div>
    </div>
  );
}
