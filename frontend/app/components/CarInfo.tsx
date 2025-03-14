import React from "react";

interface CarProps {
  car: {
    marque: {
      name: string;
      alias: string;
    };
    modele: {
      name: string;
      alias: string;  
    };
    type: {
      name: string;
      alias: string;
      puissance: number;
      carrosserie: string;
      carburant: string;
      annee: string;
    };
    seo: {
      title: string;
      description: string;
      keywords: string;
    };
  };
}

const CarInfo: React.FC<CarProps> = ({ car }) => {
  return (
    <div className="mt-4 p-4 border rounded-lg shadow-md">
      <h2 className="text-xl font-bold">{car.marque.name} {car.modele.name}</h2>
      <div className="mt-2 space-y-2">
        <p><strong>Type:</strong> {car.type.name}</p>
        <p><strong>Puissance:</strong> {car.type.puissance} ch</p>
        <p><strong>Carrosserie:</strong> {car.type.carrosserie}</p>
        <p><strong>Carburant:</strong> {car.type.carburant}</p>
        <p><strong>Année:</strong> {car.type.annee}</p>
      </div>
    </div>
  );
};

export default CarInfo;
