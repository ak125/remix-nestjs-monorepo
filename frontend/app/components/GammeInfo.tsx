import React from "react";

interface GammeProps {
  gamme: {
    name: string;
    nameMeta: string;
    alias: string;
    image: string;
    description: string;
    seo: {
      title: string;
      description: string;
      keywords: string;
    };
    content: string;
    equipementiers?: {
      id: number;
      name: string;
      logo: string;
      content: string;
    }[];
    crossLinks?: {
      id: number;
      name: string;
      alias: string;
      content: string;
    }[];
  };
}

const GammeInfo: React.FC<GammeProps> = ({ gamme }) => {
  return (
    <div className="mt-4 space-y-8">
      <section className="p-4 bg-white rounded-lg shadow-md">
        <img 
          src={gamme.image} 
          alt={gamme.nameMeta}
          className="w-40 h-40 object-cover rounded-md mx-auto"
        />
        <div className="mt-4 prose max-w-none" 
          dangerouslySetInnerHTML={{ __html: gamme.content }} 
        />
      </section>

      {gamme.equipementiers && gamme.equipementiers.length > 0 && (
        <section className="p-4 bg-gray-50 rounded-lg">
          <h2 className="text-xl font-bold mb-4">Équipementiers {gamme.name}</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {gamme.equipementiers.map(equip => (
              <div key={equip.id} className="p-4 bg-white rounded-lg">
                <img 
                  src={equip.logo}
                  alt={equip.name}
                  className="h-12 object-contain mx-auto"
                />
                <p className="mt-2 text-sm">{equip.content}</p>
              </div>
            ))}
          </div>
        </section>
      )}
    </div>
  );
};

export default GammeInfo;
