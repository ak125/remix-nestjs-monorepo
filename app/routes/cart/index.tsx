import { useState } from "react";
import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import CartModal from "@/components/CartModal";
import { Button } from "@/components/ui/button";
import { useCart } from "@/hooks/useCart";
import { getSession } from "@/utils/session.server";

// Types
interface Product {
  id: number;
  name: string;
  description?: string;
  price: number;
  priceHT: number;
  consigne?: number;
  stock: number;
  image?: string;
  reference?: string;
  isNew?: boolean;
}

// Loader côté serveur
export const loader: LoaderFunction = async ({ request }) => {
  // Vérification session
  const session = await getSession(request.headers.get("Cookie"));
  if (!session.has("sessionId")) {
    return json({ 
      error: "Session expirée", 
      requireLogin: true 
    }, { 
      status: 401 
    });
  }

  try {
    // Chargement produits depuis l'API
    const response = await fetch(`${process.env.API_URL}/products/featured`, {
      headers: {
        Cookie: request.headers.get("Cookie") || "",
      },
    });

    if (!response.ok) throw new Error("Erreur chargement produits");

    const products: Product[] = await response.json();

    return json({
      sessionId: session.get("sessionId"),
      products,
    });

  } catch (error) {
    console.error("Erreur:", error);
    return json({ 
      error: "Impossible de charger les produits" 
    }, { 
      status: 500 
    });
  }
};

export default function CartPage() {
  const { sessionId, products = [] } = useLoaderData<typeof loader>();
  const [showModal, setShowModal] = useState(false);
  const { cart, addToCart } = useCart();

  return (
    <div className="container mx-auto p-6">
      <header className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-bold">🛍️ Boutique</h1>
        
        <div className="flex items-center gap-4">
          <span className="text-sm text-gray-500">
            {cart?.items?.length || 0} article(s)
          </span>
          <Button 
            variant="outline"
            onClick={() => setShowModal(true)}
            className="flex items-center gap-2"
          >
            🛒 Voir mon panier
            {cart?.items?.length > 0 && (
              <span className="bg-primary text-white text-xs px-2 py-0.5 rounded-full">
                {cart.items.length}
              </span>
            )}
          </Button>
        </div>
      </header>

      {/* Grid produits */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        {products.map((product) => (
          <div 
            key={product.id}
            className="relative p-4 border rounded-lg shadow-sm hover:shadow-md transition-shadow"
          >
            {product.isNew && (
              <span className="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs">
                Nouveau
              </span>
            )}

            {product.image && (
              <img
                src={product.image}
                alt={product.name}
                className="w-full h-48 object-cover rounded-md mb-4"
              />
            )}

            <div className="space-y-2">
              <div className="flex justify-between">
                <h2 className="font-medium line-clamp-2">{product.name}</h2>
                <p className="text-lg font-bold">{product.price}€</p>
              </div>

              {product.reference && (
                <p className="text-sm text-gray-500">Réf: {product.reference}</p>
              )}

              {product.description && (
                <p className="text-sm text-gray-600 line-clamp-2">
                  {product.description}
                </p>
              )}

              <div className="flex items-center justify-between pt-2">
                <span className="text-sm text-gray-500">
                  Stock: {product.stock > 0 ? product.stock : 'Épuisé'}
                </span>
                <Button
                  size="sm"
                  onClick={() => {
                    addToCart(product);
                    setShowModal(true);
                  }}
                  disabled={product.stock <= 0}
                >
                  Ajouter
                </Button>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Modal panier */}
      <CartModal 
        isOpen={showModal}
        onClose={() => setShowModal(false)}
        sessionId={sessionId}
      />
    </div>
  );
}
