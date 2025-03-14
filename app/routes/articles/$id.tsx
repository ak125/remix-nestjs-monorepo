import { json, LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData, useSearchParams } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { PieceTechnicalModal } from "~/components/modals/PieceTechnicalModal";
import { useState } from "react";

export async function loader({ params, request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  
  const response = await fetch(
    `${process.env.API_URL}/articles/${params.id}?${url.searchParams}`,
    { headers: { 'Cache-Control': 'max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Article introuvable", { status: 404 });
  }

  return json(await response.json());
}

export default function ArticlePage() {
  const article = useLoaderData<typeof loader>();
  const [showTechnical, setShowTechnical] = useState(false);

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-2xl font-bold mb-6">
          {article.details.name}
        </h1>

        <div className="grid md:grid-cols-2 gap-8 mb-8">
          {/* Images */}
          <div className="space-y-4">
            {article.images.map((img, i) => (
              <img 
                key={i}
                src={img.url}
                alt={article.details.name}
                className="w-full rounded-lg shadow-md"
              />
            ))}
          </div>

          {/* Details */}
          <div className="space-y-6">
            <div>
              <h3 className="font-medium text-gray-500">Référence</h3>
              <p className="text-lg">{article.details.reference}</p>
            </div>

            <div>
              <h3 className="font-medium text-gray-500">Prix</h3>
              <p className="text-2xl font-bold text-green-600">
                {article.price.net} €
              </p>
              {article.price.consigne > 0 && (
                <p className="text-sm text-gray-500">
                  Consigne: {article.price.consigne} €
                </p>
              )}
            </div>

            <div>
              <h3 className="font-medium text-gray-500">Stock</h3>
              <p className={article.stock > 0 ? "text-green-600" : "text-red-600"}>
                {article.stock > 0 ? `${article.stock} en stock` : "Non disponible"}
              </p>
            </div>

            <Button
              onClick={() => setShowTechnical(true)}
              className="w-full"
            >
              Voir la fiche technique
            </Button>
          </div>
        </div>

        {/* Technical Modal */}
        <PieceTechnicalModal
          isOpen={showTechnical}
          onClose={() => setShowTechnical(false)}
          pieceId={article.details.id.toString()}
        />
      </div>
    </div>
  );
}
