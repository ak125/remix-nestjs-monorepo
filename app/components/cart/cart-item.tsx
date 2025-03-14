import { SwipeableListItem } from '@sandstreamdev/react-swipeable-list';
import { motion, useMotionTemplate } from 'framer-motion';
import { Trash2, Plus, Minus } from 'lucide-react';
import { useCartStore } from '~/stores/cart.store';
import { Button } from '~/components/ui/button';
import { cn } from '~/lib/utils';

interface CartItemProps {
  id: string;
  name: string;
  price: number;
  quantity: number;
  image?: string;
  onDelete: () => void;
}

export function CartItem({ id, name, price, quantity, image, onDelete }: CartItemProps) {
  const updateQuantity = useCartStore(state => state.updateQuantity);

  const swipeLeftOptions = {
    content: (
      <div className="h-full bg-red-500 flex items-center px-4">
        <Trash2 className="text-white h-5 w-5" />
      </div>
    ),
    action: onDelete
  };

  return (
    <SwipeableListItem
      swipeLeft={swipeLeftOptions}
      className="mb-2"
      threshold={0.5}
    >
      <motion.div
        layout
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        exit={{ opacity: 0, y: -20 }}
        className={cn(
          "flex items-center gap-4 p-4 bg-white",
          "dark:bg-gray-800 rounded-lg shadow-sm"
        )}
      >
        {image && (
          <img 
            src={image} 
            alt={name}
            className="w-16 h-16 object-cover rounded-md"
          />
        )}

        <div className="flex-1 min-w-0">
          <h3 className="font-medium dark:text-gray-100 truncate">
            {name}
          </h3>
          <p className="text-sm text-gray-500 dark:text-gray-400">
            {price.toFixed(2)}€
          </p>
        </div>

        <div className="flex items-center gap-2">
          <Button
            variant="outline"
            size="icon"
            className="h-8 w-8"
            onClick={() => updateQuantity(id, Math.max(0, quantity - 1))}
          >
            <Minus className="h-4 w-4" />
          </Button>

          <span className="w-8 text-center font-medium">
            {quantity}
          </span>

          <Button
            variant="outline"
            size="icon"
            className="h-8 w-8"
            onClick={() => updateQuantity(id, quantity + 1)}
          >
            <Plus className="h-4 w-4" />
          </Button>
        </div>

        <div className="w-20 text-right font-medium">
          {(price * quantity).toFixed(2)}€
        </div>
      </motion.div>
    </SwipeableListItem>
  );
}
