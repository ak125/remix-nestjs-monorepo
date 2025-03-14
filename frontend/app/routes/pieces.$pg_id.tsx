import { json, LoaderFunction, MetaFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent, CardHeader, CardTitle } from "@shadcn/ui/card";
import { Button } from "@shadcn/ui/button";

export const meta: MetaFunction = ({ data }) => {
  return {
    title: data?.seoTitle || "Pièce auto",
    description: data?.seoDescription || "Achetez votre pièce auto au meilleur prix.",
  };
};

export const loader: LoaderFunction = async ({ params }) => {
  const res = await fetch(`${process.env.BACKEND_URL}/pieces/${params.pg_id}`);
  if (!res.ok) throw new Response("Not Found", { status: 404 });
  const piece = await res.json();
  return json(piece);
};

export default function PiecePage() {
  const piece = useLoaderData();

  return (
    <div className="container mx-auto py-6">
      <Card className="max-w-3xl mx-auto">
        <CardHeader>
          <CardTitle>{piece.name}</CardTitle>
        </CardHeader>
        <CardContent>
          <p className="text-gray-500">{piece.description}</p>
          <Button className="mt-4 bg-primary hover:bg-primary-dark">
            Ajouter au panier
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
