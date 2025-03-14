import { useCartStore } from "~/stores/cart.store";
import { useCartExtrasStore } from "~/stores/cart-extras.store";
import { motion } from "framer-motion";
import { Button } from "~/components/ui/button";
import { cn } from "~/lib/utils";

export function CartRecommendations() {
  const { addItem } = useCartStore();
  const recommendations = useCartExtrasStore(state => state.recommendations);

  if (recommendations.length === 0) return null;

  return (
    <div className="mt-6 border-t pt-6">
      <h3 className="font-medium mb-4">Suggestions</h3>
      <div className="grid grid-cols-2 gap-4">
        {recommendations.map((item) => (
          <motion.div
            key={item.id}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className={cn(
              "p-4 rounded-lg bg-gray-50",
              "dark:bg-gray-800/50"
            )}
          >
            <img
              src={item.image}
              alt={item.name}
              className="w-full h-32 object-contain mb-2"
            />
            <p className="font-medium truncate">{item.name}</p>
            <div className="flex items-center justify-between mt-2">
              <span className="text-sm font-medium">
                {item.price.toFixed(2)}€
              </span>
              <Button
                variant="outline"
                size="sm"
                onClick={() => addItem(item)}
              >
                Ajouter
              </Button>
            </div>
          </motion.div>
        ))}
      </div>
    </div>
  );
}
