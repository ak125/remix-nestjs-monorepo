import { json, LoaderFunction, MetaFunction } from "@remix-run/node";
import { useLoaderData, Link, useCatch } from "@remix-run/react";
import { prisma } from "~/utils/database.server";
import { generateMetaTags } from "~/utils/seo.server";

type LoaderData = {
  marque: {
    id: number;
    name: string;
    alias: string;
    logo: string;
  };
  modele: {
    id: number;
    name: string;
    alias: string;
    display: boolean;
  };
  types?: Array<{
    id: number;
    name: string;
    alias: string;
    fuel?: string | null;
    powerPS?: number | null;
    yearFrom?: number | null;
    yearTo?: number | null;
  }>;
};

export const loader: LoaderFunction = async ({ params }) => {
  const { marqueAlias, mdgAlias } = params;

  if (!marqueAlias || !mdgAlias) {
    throw new Response("Paramètres URL manquants", { status: 400 });
  }

  const modele = await prisma.modele.findFirst({
    where: {
      alias: mdgAlias,
      marque: {
        alias: marqueAlias,
        display: true,
      },
      display: true,
    },
    include: {
      marque: true,
      types: {
        where: {
          display: true
        },
        orderBy: {
          name: 'asc'
        }
      },
    },
  });

  if (!modele) {
    throw new Response("Modèle non trouvé", { status: 404 });
  }

  return json({
    marque: modele.marque,
    modele: {
      id: modele.id,
      name: modele.name,
      alias: modele.alias,
      display: modele.display
    },
    types: modele.types
  });
};

// 🔥 SEO dynamique pour chaque modèle
export const meta: MetaFunction = ({ data }) => {
  if (!data) return {};
  
  const { marque, modele } = data as LoaderData;
  
  return generateMetaTags({
    title: `Pièces auto ${marque.name} ${modele.name} à prix pas cher`,
    description: `Achetez des pièces détachées pour ${marque.name} ${modele.name} en ligne au meilleur prix. Livraison rapide et garantie fabricant.`,
    keywords: `${marque.name}, ${modele.name}, pièces auto, accessoires auto, pièces détachées`,
    image: `/uploads/logos/${marque.logo}`,
    canonicalUrl: `https://www.automecanik.com/blog/${marque.alias}/${modele.alias}`
  });
};

export default function ModelePage() {
  const { marque, modele, types } = useLoaderData<LoaderData>();

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="flex items-center mb-8">
        <img 
          src={`/uploads/logos/${marque.logo}`} 
          alt={`Logo ${marque.name}`} 
          width="100"
          height="60" 
          className="mr-4"
        />
        <h1 className="text-3xl font-bold">
          🚗 {marque.name} {modele.name}
        </h1>
      </div>

      <div className="bg-white rounded-lg shadow-md p-6 mb-8">
        <p className="text-lg mb-4">
          Trouvez toutes les pièces auto pour votre {marque.name} {modele.name} à prix réduit !
          Nous proposons une large gamme de pièces détachées compatibles avec votre véhicule.
        </p>
        
        <Link 
          to={`/pieces-detachees/${marque.alias}`}
          className="text-blue-600 hover:underline"
        >
          Voir toutes les pièces pour {marque.name}
        </Link>
      </div>

      {types && types.length > 0 && (
        <div className="mb-8">
          <h2 className="text-2xl font-semibold mb-4">Versions disponibles</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {types.map(type => (
              <div key={type.id} className="bg-gray-50 p-4 rounded-md border border-gray-200">
                <h3 className="font-medium">{type.name}</h3>
                <ul className="text-sm text-gray-600 mt-2">
                  {type.fuel && <li>Carburant: {type.fuel}</li>}
                  {type.powerPS && <li>Puissance: {type.powerPS} ch</li>}
                  {(type.yearFrom || type.yearTo) && (
                    <li>
                      Années: {type.yearFrom || '?'} - {type.yearTo || 'présent'}
                    </li>
                  )}
                </ul>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}

// Gestion des erreurs
export function CatchBoundary() {
  const caught = useCatch();
  
  return (
    <div className="container mx-auto p-8 text-center">
      <h1 className="text-2xl font-bold text-red-500 mb-4">
        {caught.status === 404 ? "Modèle non trouvé" : "Erreur"}
      </h1>
      <p className="mb-6">{caught.data}</p>
      <Link to="/blog" className="text-blue-500 hover:underline">
        Retour à la liste des modèles
      </Link>
    </div>
  );
}
