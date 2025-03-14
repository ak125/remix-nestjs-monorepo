import { json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { prisma } from "~/lib/prisma.server";
import { CompareTable } from "~/components/car/CompareTable";

export const loader = async ({ request }: { request: Request }) => {
  const url = new URL(request.url);
  const modelIds = url.searchParams.getAll("models").map(Number);

  if (!modelIds.length) {
    return json({ models: [] });
  }

  const models = await prisma.autoModele.findMany({
    where: {
      id: { in: modelIds }
    },
    include: {
      specifications: true,
      marque: true
    }
  });

  return json({ models });
};

export default function ComparatorPage() {
  const { models } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold mb-8">Comparateur de Modèles</h1>
      
      {models.length > 0 ? (
        <CompareTable models={models} />
      ) : (
        <p className="text-muted-foreground">
          Sélectionnez des modèles à comparer
        </p>
      )}
    </div>
  );
}
