import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export async function loader({ request }) {
  const url = new URL(request.url);
  const query = url.searchParams.get("q");
  
  if (!query || query.length < 2) {
    return json({ results: [] });
  }

  try {
    // Check cache first for better performance
    const cacheKey = `search:${query.toLowerCase()}`;
    const cachedResults = await cache.get(cacheKey);
    
    if (cachedResults) {
      return json({ results: cachedResults });
    }
    
    // Build search parameters
    const searchTerm = `%${query.toLowerCase()}%`;
    
    // Perform the search across different tables
    const [products, categories, brands] = await Promise.all([
      // Search products
      prisma.$queryRaw`
        SELECT id, name, "catalogNumber" as "catalogNumber", "type" 
        FROM products 
        WHERE LOWER(name) LIKE ${searchTerm} 
        OR LOWER("catalogNumber") LIKE ${searchTerm}
        LIMIT 5
      `,
      
      // Search categories
      prisma.$queryRaw`
        SELECT id, name, 'category' as "type"
        FROM categories
        WHERE LOWER(name) LIKE ${searchTerm}
        LIMIT 3
      `,
      
      // Search brands
      prisma.$queryRaw`
        SELECT id, name, 'brand' as "type"
        FROM brands
        WHERE LOWER(name) LIKE ${searchTerm}
        LIMIT 3
      `,
    ]);
    
    // Combine and format results
    const results = [
      ...products,
      ...categories,
      ...brands
    ].map(item => ({
      id: item.id,
      name: item.name,
      type: item.type || 'product',
      url: getUrlForItem(item),
      catalogNumber: item.catalogNumber
    }));
    
    // Cache results for 5 minutes
    await cache.set(cacheKey, results, 300);
    
    return json({ results });
  } catch (error) {
    console.error("Search error:", error);
    return json({ error: "Une erreur est survenue" }, { status: 500 });
  }
}

// Helper to generate URLs for different entity types
function getUrlForItem(item: any): string {
  switch(item.type) {
    case 'category':
      return `/categories/${item.id}`;
    case 'brand':
      return `/marques/${item.id}`;
    default:
      return `/produits/${item.id}`;
  }
}
