import { json, LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "~/components/ui/tabs";

export async function loader({ params, request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const searchParams = new URLSearchParams({
    typeId: url.searchParams.get("typeId") || "",
    pgId: url.searchParams.get("pgId") || "",
    pmId: url.searchParams.get("pmId") || ""
  });

  const response = await fetch(
    `${process.env.API_URL}/articles/${params.pieceId}?${searchParams}`,
    { headers: { 'Cache-Control': 'public, max-age=300' } }
  );

  if (!response.ok) {
    throw new Response("Article introuvable", { status: 404 });
  }

  return json(await response.json());
}

export default function ArticlePage() {
  const data = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-4xl mx-auto">
        <div className="grid md:grid-cols-2 gap-8">
          {/* Images */}
          <div className="space-y-4">
            {data.images.map((img, i) => (
              <img
                key={i}
                src={img.url}
                alt={data.article.name}
                className="w-full rounded-lg shadow-md"
              />
            ))}
          </div>

          {/* Details */}
          <div className="space-y-6">
            <div>
              <h1 className="text-2xl font-bold">{data.article.name}</h1>
              <p className="text-sm text-gray-500">{data.article.ref}</p>
            </div>

            {/* Prix et Stock */}
            <div className="space-y-2">
              <h3 className="font-medium">Prix</h3>
              {data.price.discount > 0 && (
                <p className="text-sm text-gray-500">
                  Public: {data.price.public}€
                  <span className="ml-2 text-red-500">-{data.price.discount}%</span>
                </p>
              )}
              <p className="text-2xl font-bold text-green-600">
                {data.price.net}€
              </p>
              <p className={data.stock > 0 ? "text-green-600" : "text-red-500"}>
                {data.stock > 0 ? `${data.stock} en stock` : "Non disponible"}
              </p>
            </div>

            {/* Actions */}
            <div className="space-y-4">
              <Button className="w-full">
                Ajouter au panier
              </Button>
            </div>
          </div>
        </div>

        {/* Technical Tabs */}
        <Tabs defaultValue="tech" className="mt-8">
          <TabsList>
            <TabsTrigger value="tech">Caractéristiques</TabsTrigger>
            <TabsTrigger value="refs">Références</TabsTrigger>
          </TabsList>
          
          <TabsContent value="tech" className="space-y-4">
            {data.criteria.map((crit, i) => (
              <div key={i} className="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                <span className="text-gray-600">{crit.name}</span>
                <span>{crit.value} {crit.unit}</span>
              </div>
            ))}
          </TabsContent>

          <TabsContent value="refs">
            <div className="space-y-6">
              <div>
                <h3 className="font-medium mb-2">Références OEM</h3>
                <div className="space-y-2">
                  {data.references.oem.map((ref, i) => (
                    <div key={i} className="flex justify-between p-2 bg-gray-50 rounded">
                      <span>{ref.maker}</span>
                      <span className="font-mono">{ref.ref}</span>
                    </div>
                  ))}
                </div>
              </div>

              <div>
                <h3 className="font-medium mb-2">Références équipementiers</h3>
                <div className="space-y-2">
                  {data.references.equip.map((ref, i) => (
                    <div key={i} className="flex justify-between p-2 bg-gray-50 rounded">
                      <span>{ref.maker}</span>
                      <span className="font-mono">{ref.ref}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
}
