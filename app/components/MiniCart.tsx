import { Link, useFetcher } from "@remix-run/react";
import { useEffect, useState } from "react";
import type { Cart } from "~/utils/cart.server";

type MiniCartProps = {
  initialCart?: Cart;
};

export default function MiniCart({ initialCart }: MiniCartProps) {
  const [cart, setCart] = useState<Cart | null>(initialCart || null);
  const fetcher = useFetcher();
  
  // Récupère les données du panier au chargement si non fournies
  useEffect(() => {
    if (!initialCart && !cart) {
      fetcher.load("/api/cart");
    }
  }, [initialCart, cart]);
  
  // Met à jour le panier quand les données sont chargées
  useEffect(() => {
    if (fetcher.data) {
      setCart(fetcher.data);
    }
  }, [fetcher.data]);
  
  // Si pas de données, affiche loader
  if (!cart) {
    return <div className="mini-cart-loading">Chargement...</div>;
  }
  
  const itemCount = cart.items.reduce((sum, item) => sum + item.quantity, 0);
  
  return (
    <div className="mini-cart">
      <Link to="/cart" className="flex items-center">
        <span className="relative">
          🛒
          {itemCount > 0 && (
            <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
              {itemCount}
            </span>
          )}
        </span>
        <span className="ml-2">
          {(cart.total + cart.totalConsigne).toFixed(2)} €
        </span>
      </Link>
    </div>
  );
}
