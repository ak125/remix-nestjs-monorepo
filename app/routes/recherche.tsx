import { useLoaderData, useSearchParams } from "@remix-run/react";
import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import { QuickSearch } from "~/components/search/quick-search";
import { ProductCard } from "~/components/product/product-card";

export async function loader({ request }) {
  const url = new URL(request.url);
  const query = url.searchParams.get("q");
  
  if (!query) {
    return json({ 
      results: [],
      categories: [],
      brands: [],
      query: ""
    });
  }
  
  try {
    // Prepare search term
    const searchTerm = `%${query.toLowerCase()}%`;
    
    // Execute searches in parallel
    const [products, categories, brands] = await Promise.all([
      // Search products
      prisma.product.findMany({
        where: {
          OR: [
            { name: { contains: query, mode: 'insensitive' } },
            { description: { contains: query, mode: 'insensitive' } },
            { catalogNumber: { contains: query, mode: 'insensitive' } }
          ]
        },
        include: {
          brand: true,
          category: true,
          images: { take: 1 }
        },
        take: 24
      }),
      
      // Related categories
      prisma.category.findMany({
        where: {
          name: { contains: query, mode: 'insensitive' }
        },
        take: 5
      }),
      
      // Related brands
      prisma.brand.findMany({
        where: {
          name: { contains: query, mode: 'insensitive' }
        },
        take: 5
      })
    ]);
    
    return json({
      results: products,
      categories,
      brands,
      query
    });
    
  } catch (error) {
    console.error("Search page error:", error);
    return json({ 
      error: "Une erreur est survenue pendant la recherche",
      results: [],
      categories: [],
      brands: [],
      query
    });
  }
}

export default function SearchPage() {
  const { results, categories, brands, query, error } = useLoaderData<typeof loader>();
  const [searchParams] = useSearchParams();
  const searchQuery = searchParams.get("q") || "";
  
  return (
    <div className="container px-4 py-8">
      <h1 className="text-3xl font-bold mb-6">Résultats de recherche</h1>
      
      <div className="mb-8">
        <QuickSearch className="max-w-2xl" />
      </div>
      
      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md mb-8">
          {error}
        </div>
      )}
      
      {searchQuery && !error && (
        <>
          <p className="text-lg mb-8">
            {results.length} résultat(s) pour "{searchQuery}"
          </p>
          
          {/* Related categories */}
          {categories.length > 0 && (
            <div className="mb-8">
              <h2 className="text-xl font-semibold mb-4">Catégories associées</h2>
              <div className="flex flex-wrap gap-2">
                {categories.map(category => (
                  <a 
                    key={category.id} 
                    href={`/categories/${category.id}`}
                    className="bg-muted px-3 py-1 rounded-full text-sm hover:bg-muted/80"
                  >
                    {category.name}
                  </a>
                ))}
              </div>
            </div>
          )}
          
          {/* Related brands */}
          {brands.length > 0 && (
            <div className="mb-8">
              <h2 className="text-xl font-semibold mb-4">Marques associées</h2>
              <div className="flex flex-wrap gap-2">
                {brands.map(brand => (
                  <a 
                    key={brand.id} 
                    href={`/marques/${brand.id}`}
                    className="bg-muted px-3 py-1 rounded-full text-sm hover:bg-muted/80"
                  >
                    {brand.name}
                  </a>
                ))}
              </div>
            </div>
          )}
          
          {/* Results */}
          {results.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {results.map(product => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          ) : (
            <div className="text-center py-12">
              <p className="text-2xl font-semibold mb-2">Aucun produit trouvé</p>
              <p className="text-muted-foreground">
                Essayez avec d'autres termes ou parcourez nos catégories
              </p>
            </div>
          )}
        </>
      )}
    </div>
  );
}
