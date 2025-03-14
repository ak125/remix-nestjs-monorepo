import { useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { useCartStore } from "~/stores/cart.store";
import { Button } from "~/components/ui/button";
import { Trash2, Undo, ShoppingCart } from "lucide-react";
import { useToast } from "~/hooks/use-toast";
import { SwipeableList } from '@sandstreamdev/react-swipeable-list';
import { CartItem } from "./cart-item";
import { cn } from "~/lib/utils";

export function Cart() {
  const { items, removeItem, clearCart, undoLastRemoval, lastRemovedItem } = useCartStore();
  const { toast } = useToast();
  
  const total = items.reduce((sum, item) => sum + item.price * item.quantity, 0);

  return (
    <div className="flex flex-col h-full">
      <div className="p-4 border-b">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-semibold flex items-center gap-2">
            <ShoppingCart className="h-5 w-5" />
            Votre Panier
          </h2>
          {items.length > 0 && (
            <Button
              variant="ghost"
              size="sm"
              className="text-red-600"
              onClick={() => {
                if (confirm("Voulez-vous vraiment vider votre panier ?")) {
                  clearCart();
                  toast({
                    title: "Panier vidé",
                    description: "Votre panier a été vidé avec succès"
                  });
                }
              }}
            >
              <Trash2 className="h-4 w-4 mr-2" />
              Vider
            </Button>
          )}
        </div>
      </div>

      <div className="flex-1 overflow-auto p-4">
        <AnimatePresence mode="popLayout">
          {items.length > 0 ? (
            <SwipeableList>
              {items.map(item => (
                <CartItem
                  key={item.id}
                  {...item}
                  onDelete={() => {
                    removeItem(item.id);
                    toast({
                      title: "Article supprimé",
                      description: (
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => undoLastRemoval()}
                        >
                          <Undo className="h-4 w-4 mr-2" />
                          Annuler
                        </Button>
                      )
                    });
                  }}
                />
              ))}
            </SwipeableList>
          ) : (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center text-gray-500 mt-8"
            >
              Votre panier est vide
            </motion.div>
          )}
        </AnimatePresence>
      </div>

      {items.length > 0 && (
        <div className="p-4 border-t space-y-4">
          <div className="flex justify-between text-lg font-medium">
            <span>Total</span>
            <span>{total.toFixed(2)} €</span>
          </div>
          <Button className="w-full">
            Commander
          </Button>
        </div>
      )}
    </div>
  );
}
