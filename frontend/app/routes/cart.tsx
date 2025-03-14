import { useFetcher, useNavigate } from "@remix-run/react";
import { useEffect, useState } from "react";
import { z } from "zod";
import { cartService } from "~/services/cart.service";

// Validation schemas
const CartItemSchema = z.object({
  id: z.string(),
  pieceId: z.string(),
  quantity: z.number().min(1),
  priceHT: z.number().positive(),
  priceTTC: z.number().positive(), 
  consigneHT: z.number().default(0),
  consigneTTC: z.number().default(0),
  name: z.string(),
  ref: z.string(),
  image: z.string().optional(),
});

const CartSchema = z.object({
  id: z.string(),
  items: z.array(CartItemSchema),
  totalAmount: z.number(),
  totalConsigne: z.number(),
  updatedAt: z.string().datetime(),
});

type Cart = z.infer<typeof CartSchema>;

export default function CartPage() {
  const fetcher = useFetcher();
  const navigate = useNavigate();
  const [cart, setCart] = useState<Cart | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  // Chargement initial
  useEffect(() => {
    const loadCart = async () => {
      try {
        const userId = localStorage.getItem('userId');
        if (!userId) {
          navigate('/login');
          return;
        }

        const data = await cartService.getCart(userId);
        const result = CartSchema.safeParse(data);
        
        if (result.success) {
          setCart(result.data);
          setError(null);
        } else {
          setError('Format de données invalide');
        }
      } catch (err) {
        setError('Erreur chargement panier');
      } finally {
        setIsLoading(false);
      }
    };

    loadCart();
  }, [navigate]);

  // Mise à jour quantité
  const updateQuantity = async (itemId: string, newQuantity: number) => {
    if (newQuantity < 1) return;
    
    const userId = localStorage.getItem('userId');
    if (!userId) return;

    try {
      const data = await cartService.updateQuantity(userId, itemId, newQuantity);
      const result = CartSchema.safeParse(data);
      
      if (result.success) {
        setCart(result.data);
        setError(null);
      }
    } catch (err) {
      setError('Erreur mise à jour quantité');
    }
  };

  // Supprimer article
  const removeItem = async (itemId: string) => {
    const userId = localStorage.getItem('userId');
    if (!userId) return;

    try {
      const data = await cartService.removeItem(userId, itemId);
      const result = CartSchema.safeParse(data);
      
      if (result.success) {
        setCart(result.data);
        setError(null);
      }
    } catch (err) {
      setError('Erreur suppression article');
    }
  };

  if (isLoading) {
    return (
      <div className="flex justify-center items-center h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent" />
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold mb-6">Votre Panier</h1>

      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {error}
        </div>
      )}

      {cart?.items.length ? (
        <div className="space-y-4">
          {/* Articles */}
          {cart.items.map((item) => (
            <div 
              key={item.id}
              className="flex items-center justify-between p-4 border rounded shadow-sm"
            >
              {/* Image & Infos */}
              <div className="flex items-center space-x-4">
                <img
                  src={item.image || "/placeholder.jpg"}
                  alt={item.name}
                  className="w-16 h-16 object-cover rounded"
                />
                <div>
                  <h3 className="font-medium">{item.name}</h3>
                  <p className="text-sm text-gray-600">Réf: {item.ref}</p>
                </div>
              </div>

              {/* Quantité */}
              <div className="flex items-center space-x-2">
                <button
                  onClick={() => updateQuantity(item.id, item.quantity - 1)}
                  className="p-1 rounded border hover:bg-gray-100"
                >
                  -
                </button>
                <span className="w-8 text-center">{item.quantity}</span>
                <button
                  onClick={() => updateQuantity(item.id, item.quantity + 1)}
                  className="p-1 rounded border hover:bg-gray-100"
                >
                  +
                </button>
              </div>

              {/* Prix */}
              <div className="text-right min-w-[100px]">
                <div>{(item.priceTTC * item.quantity).toFixed(2)} €</div>
                {item.consigneTTC > 0 && (
                  <div className="text-sm text-gray-600">
                    Consigne: {item.consigneTTC.toFixed(2)} €
                  </div>
                )}
              </div>

              {/* Supprimer */}
              <button
                onClick={() => removeItem(item.id)}
                className="ml-4 text-red-600 hover:text-red-800"
              >
                <span className="sr-only">Supprimer</span>
                ×
              </button>
            </div>
          ))}

          {/* Total */}
          <div className="mt-6 text-right">
            <div className="text-lg font-medium">
              Total: {cart.totalAmount.toFixed(2)} €
            </div>
            {cart.totalConsigne > 0 && (
              <div className="text-sm text-gray-600">
                Dont consigne: {cart.totalConsigne.toFixed(2)} €
              </div>
            )}
          </div>

          {/* Actions */}
          <div className="mt-8 flex justify-end space-x-4">
            <button
              onClick={() => navigate('/products')}
              className="px-4 py-2 border rounded hover:bg-gray-50"
            >
              Continuer mes achats
            </button>
            <button
              onClick={() => navigate('/checkout')}
              className="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark"
              disabled={!cart.items.length}
            >
              Commander
            </button>
          </div>
        </div>
      ) : (
        <div className="text-center py-12">
          <p className="text-gray-500 mb-4">Votre panier est vide</p>
          <button
            onClick={() => navigate('/products')}
            className="px-4 py-2 border rounded hover:bg-gray-50"
          >
            Découvrir nos produits
          </button>
        </div>
      )}
    </div>
  );
}
