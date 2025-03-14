import { useParams } from "@remix-run/react";

export default function ErreurPage() {
  const params = useParams();
  const isError410 = params["*"] === "410";

  return (
    <div className="container mx-auto p-8 text-center">
      <h1 className="text-4xl font-bold text-red-500 mb-4">
        {isError410 ? "Erreur 410" : "Erreur 412"}
      </h1>
      
      {isError410 ? (
        <>
          <h2 className="text-2xl font-semibold mb-4">
            Page supprimée
          </h2>
          <p className="text-gray-600">
            Cette page a été supprimée ou déplacée définitivement. 
            Veuillez utiliser la navigation pour trouver ce que vous cherchez.
          </p>
        </>
      ) : (
        <>
          <h2 className="text-2xl font-semibold mb-4">
            Page temporairement indisponible
          </h2>
          <p className="text-gray-600">
            Cette page est temporairement indisponible. 
            Veuillez réessayer ultérieurement.
          </p>
        </>
      )}
    </div>
  );
}
