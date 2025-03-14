import { json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { prisma } from "~/lib/db.server";

// Components
import { MarquesCarousel } from "~/components/home/marques-carousel";
import { EquipementiersSection } from "~/components/home/equipementiers-section";
import { CatalogSection } from "~/components/home/catalog-section";
import { WelcomeSection } from "~/components/home/welcome-section";

export async function loader() {
  // Récupérer toutes les données nécessaires pour la page d'accueil
  const [marques, equipementiers, catalogFamilies] = await Promise.all([
    // Marques automobiles
    prisma.marque.findMany({
      where: { 
        display: true, 
        NOT: { id: { in: [339, 441] } } // Exclure certaines marques comme dans le code original
      },
      orderBy: { sort: 'asc' },
      select: {
        id: true, 
        name: true, 
        nameMeta: true, 
        alias: true, 
        logo: true,
        top: true
      }
    }),
    
    // Équipementiers mis en avant
    prisma.equipementier.findMany({
      where: { 
        display: true, 
        top: true 
      },
      orderBy: { sort: 'asc' },
      select: {
        id: true,
        name: true,
        nameMeta: true,
        logo: true,
        preview: true
      }
    }),
    
    // Familles de catalogue avec leurs gammes
    prisma.catalogFamily.findMany({
      where: { display: true },
      select: {
        id: true,
        name: true,
        nameSystem: true,
        description: true,
        image: true,
        gammes: {
          include: {
            gamme: true
          },
          where: {
            gamme: {
              display: true,
              level: 1
            }
          }
        }
      },
      take: 6 // Limiter à 6 familles pour la page d'accueil
    })
  ]);
  
  return json({
    marques,
    equipementiers,
    catalogFamilies
  });
}

export default function Index() {
  const { marques, equipementiers, catalogFamilies } = useLoaderData<typeof loader>();

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <section className="bg-gradient-to-b from-blue-900 to-blue-700 text-white py-16">
        <div className="container mx-auto px-4">
          <h1 className="text-4xl font-bold mb-4">Votre spécialiste en pièces auto</h1>
          <p className="text-xl mb-8">
            Découvrez notre catalogue de pièces détachées automobile neuves et d'origine
          </p>
          
          {/* Car Search Form - This would be a separate component */}
          <div className="bg-white p-6 rounded-lg shadow-lg text-gray-800">
            <h2 className="text-lg font-semibold mb-4">Recherchez par véhicule</h2>
            {/* Car search form would go here */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label className="block text-sm font-medium mb-1">Marque</label>
                <select className="w-full p-2 border rounded">
                  <option>Sélectionner une marque</option>
                  {marques.map(marque => (
                    <option key={marque.id} value={marque.id}>
                      {marque.name}
                    </option>
                  ))}
                </select>
              </div>
              
              {/* Additional form fields would go here */}
              <div>
                <label className="block text-sm font-medium mb-1">Modèle</label>
                <select className="w-full p-2 border rounded" disabled>
                  <option>Sélectionner un modèle</option>
                </select>
              </div>
              
              <div>
                <label className="block text-sm font-medium mb-1">Année</label>
                <select className="w-full p-2 border rounded" disabled>
                  <option>Sélectionner une année</option>
                </select>
              </div>
              
              <div>
                <label className="block text-sm font-medium mb-1">Type</label>
                <select className="w-full p-2 border rounded" disabled>
                  <option>Sélectionner un type</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      {/* Marques Carousel */}
      <section className="py-12 bg-white">
        <div className="container mx-auto px-4">
          <MarquesCarousel 
            marques={marques} 
            title="Marques automobile" 
          />
          
          <div className="mt-8 text-center">
            <p className="text-gray-600 max-w-3xl mx-auto">
              Automecanik vous propose toutes les marques des constructeurs automobiles européens et étrangers 
              vendu en Europe et plus précisément sur le marché français, présenté par ordre alphabétique 
              et selon le logo de la marque constructeur de votre véhicule.
            </p>
          </div>
        </div>
      </section>
      
      {/* Catalog Section */}
      <section className="py-12 bg-gray-50">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold mb-8 text-center">
            Catalogue Pièces détachées auto
          </h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {catalogFamilies.map((family) => (
              <div key={family.id} className="bg-white rounded-lg shadow-md overflow-hidden">
                <div className="p-4">
                  <div className="flex items-center space-x-4">
                    <img
                      src={`/upload/articles/familles-produits/${family.image}`}
                      alt={family.name}
                      className="w-16 h-16 object-contain"
                      loading="lazy"
                    />
                    <h3 className="font-bold text-lg">{family.nameSystem || family.name}</h3>
                  </div>
                  
                  <div className="mt-4">
                    <ul className="space-y-1 text-sm">
                      {family.gammes.slice(0, 5).map((item) => (
                        <li key={`${family.id}-${item.gamme.id}`}>
                          <a 
                            href={`/piece/${item.gamme.alias}-${item.gamme.id}.html`}
                            className="text-blue-600 hover:underline"
                          >
                            {item.gamme.name}
                          </a>
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
      
      {/* Equipementiers Section */}
      <section className="py-12 bg-white">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold mb-8 text-center">
            Marque d'équipementiers d'origine et première monte
          </h2>
          
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            {equipementiers.map((equip) => (
              <div 
                key={equip.id} 
                className="bg-white border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col items-center"
              >
                <img
                  src={`/upload/equipementiers-automobiles/${equip.logo}`}
                  alt={equip.name}
                  className="h-12 mb-4 object-contain"
                  loading="lazy"
                />
                <h3 className="font-semibold text-center">{equip.name}</h3>
                {equip.preview && (
                  <p className="text-sm text-gray-600 text-center mt-2">
                    {equip.preview}
                  </p>
                )}
              </div>
            ))}
          </div>
          
          <div className="mt-8 text-center">
            <p className="text-gray-600 max-w-3xl mx-auto">
              Automecanik vous propose des pièces fabriquées par les plus grands équipementiers automobiles, 
              garantissant qualité et fiabilité pour l'entretien de votre véhicule.
            </p>
          </div>
        </div>
      </section>
      
      {/* About Section */}
      <section className="py-12 bg-gray-50">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold mb-6 text-center">
            Automecanik, votre magasin en ligne des pièces détachées
          </h2>
          
          <div className="prose max-w-3xl mx-auto">
            <p>
              Automecanik vous propose plusieurs références de pièces auto neuves et d'origine avec le meilleur rapport qualité/prix. 
              Toutes vos pièces auto se trouvent dans un catalogue en ligne, groupées dans divers catégories : freinage (plaquettes, disques, étrier de frein...), 
              filtration (filtre à huile, filtre à carburant...), moteur, direction/suspension, transmission, refroidissement (radiateur, pompe à eau...), 
              éclairage, climatisation/ventilation, pièces électriques (alternateur, démarreur, vanne EGR, débitmétre...) et accessoires.
            </p>
            
            <p>
              Quelques soit le type de motorisation essence ou diesel, Automecanik vend pièces auto compatibles avec tous les constructeurs 
              automobiles du marché tels que : Renault, Peugeot, Citroën, Audi, Fiat, Volkswagen, Ford, BMW, Mercedes, Alfa Romeo, Opel, Seat...etc.
            </p>
            
            <p>
              Toutes les pièces disponibles dans notre catalogue sont garanties par les plus grands équipementiers de pièces détachées automobile 
              et conformes aux normes européenne comme Bosch, Valeo, Luk, Sachs, Delphi, Febi, SKF, TRW, SNR, Gates, Dayco, Continental, Magneti Marelli, 
              Walker, Bosal, Bendix, ATE, Hella, Beru, Goodyear, Lizarte...etc.
            </p>
          </div>
        </div>
      </section>
    </div>
  );
}
