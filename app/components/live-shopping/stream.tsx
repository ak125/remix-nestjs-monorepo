import { useEffect, useRef } from 'react';
import { motion } from 'framer-motion';
import { useLiveShoppingStore } from '~/stores/live-shopping.store';
import { useCartStore } from '~/stores/cart.store';
import { Button } from '~/components/ui/button';
import { Input } from '~/components/ui/input';
import { Users, Send, ShoppingCart } from 'lucide-react';

interface StreamProps {
  productId: string;
  streamUrl: string;
}

export function LiveStream({ productId, streamUrl }: StreamProps) {
  const videoRef = useRef<HTMLVideoElement>(null);
  const chatRef = useRef<HTMLDivElement>(null);
  const {
    currentSession,
    chat,
    addChatMessage,
    updateViewerCount
  } = useLiveShoppingStore();
  const addToCart = useCartStore(state => state.addItem);

  useEffect(() => {
    if (videoRef.current) {
      videoRef.current.src = streamUrl;
    }

    const interval = setInterval(() => {
      updateViewerCount(Math.floor(Math.random() * 100 + 50));
    }, 5000);

    return () => clearInterval(interval);
  }, [streamUrl, updateViewerCount]);

  return (
    <div className="grid grid-cols-3 gap-4 h-[600px]">
      <div className="col-span-2 relative">
        <video
          ref={videoRef}
          autoPlay
          className="w-full h-full object-cover rounded-lg"
        />
        <div className="absolute top-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full flex items-center gap-2">
          <Users className="h-4 w-4" />
          {currentSession?.viewerCount || 0}
        </div>
      </div>

      <div className="flex flex-col bg-gray-50 rounded-lg p-4">
        <div className="flex-1 overflow-auto" ref={chatRef}>
          {chat.map(msg => (
            <motion.div
              key={msg.id}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              className="mb-2 p-2 bg-white rounded"
            >
              <p className="text-sm">{msg.message}</p>
            </motion.div>
          ))}
        </div>

        <div className="mt-4 flex gap-2">
          <Input
            placeholder="Votre message..."
            onKeyPress={(e) => {
              if (e.key === 'Enter') {
                addChatMessage(e.currentTarget.value, 'user-id');
                e.currentTarget.value = '';
              }
            }}
          />
          <Button size="icon">
            <Send className="h-4 w-4" />
          </Button>
        </div>

        <Button
          className="mt-4"
          onClick={() => addToCart(productId)}
        >
          <ShoppingCart className="h-4 w-4 mr-2" />
          Ajouter au panier
        </Button>
      </div>
    </div>
  );
}
