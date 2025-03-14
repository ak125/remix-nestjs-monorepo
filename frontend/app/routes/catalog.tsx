import { json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Suspense, useState } from "react";
import { prisma } from "~/lib/prisma.server";
import { CategoryList } from "~/components/catalog/CategoryList";
import { CategorySkeleton } from "~/components/catalog/CategorySkeleton";

export const loader = async () => {
  const categories = await prisma.category.findMany({
    where: { isActive: true },
    include: { 
      gammes: {
        where: { isActive: true },
        orderBy: { sortOrder: 'asc' }
      }
    },
    orderBy: { sortOrder: 'asc' }
  });

  return json({ categories });
};

export default function CatalogPage() {
  const { categories } = useLoaderData<typeof loader>();
  
  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold mb-8">Catalogue des Gammes</h1>
      
      <Suspense fallback={<CategorySkeleton />}>
        <CategoryList categories={categories} />
      </Suspense>
    </div>
  );
}
