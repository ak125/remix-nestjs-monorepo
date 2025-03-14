import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { supabase } from '~/lib/supabase.client';

interface CartItem {
  id: string;
  name: string;
  price: number;
  quantity: number;
  image?: string;
}

interface PromoCode {
  code: string;
  discount: number;
  minAmount?: number;
}

const PROMO_CODES: Record<string, PromoCode> = {
  'WELCOME10': { code: 'WELCOME10', discount: 10 },
  'BLACKFRIDAY': { code: 'BLACKFRIDAY', discount: 20, minAmount: 100 }
};

interface CartState extends CartStore {
  promoCode: PromoCode | null;
  applyPromoCode: (code: string) => void;
  removePromoCode: () => void;
  getTotal: () => { subtotal: number; discount: number; total: number };
}

interface CartStore {
  items: CartItem[];
  isLoading: boolean;
  addItem: (item: Omit<CartItem, 'quantity'>) => Promise<void>;
  removeItem: (id: string) => Promise<void>;
  updateQuantity: (id: string, quantity: number) => Promise<void>;
  clearCart: () => Promise<void>;
  syncWithSupabase: (userId: string) => Promise<void>;
  initRealtimeSync: (userId: string) => () => void;
  lastRemovedItem: CartItem | null;
  undoLastRemoval: () => void;
}

export const useCartStore = create<CartState>()(
  persist(
    (set, get) => {
      let lastRemovedItem: CartItem | null = null;

      return {
        items: [],
        isLoading: false,
        lastRemovedItem: null,
        promoCode: null,

        syncCart: async (userId: string) => {
          const { data: cartItems } = await supabase
            .from('shopping_carts')
            .select('items')
            .eq('user_id', userId)
            .single();

          if (cartItems) {
            set({ items: cartItems.items });
          }

          // Subscribe to realtime changes
          const channel = supabase
            .channel(`cart:${userId}`)
            .on(
              'postgres_changes',
              {
                event: '*',
                schema: 'public',
                table: 'shopping_carts',
                filter: `user_id=eq.${userId}`
              },
              (payload) => {
                if (payload.new?.items) {
                  set({ items: payload.new.items });
                }
              }
            )
            .subscribe();

          return () => {
            channel.unsubscribe();
          };
        },

        addItem: async (item) => {
          const { data: { user } } = await supabase.auth.getUser();
          if (!user) return;

          const newItems = [...get().items, { ...item, quantity: 1 }];
          set({ items: newItems });

          await supabase
            .from('shopping_carts')
            .upsert({
              user_id: user.id,
              items: newItems
            });
        },

        removeItem: async (id) => {
          const { data: { user } } = await supabase.auth.getUser();
          if (!user) return;

          const newItems = get().items.filter(item => item.id !== id);
          set({ items: newItems });

          await supabase
            .from('shopping_carts')
            .upsert({
              user_id: user.id,
              items: newItems
            });
        },

        updateQuantity: async (id, quantity) => {
          const { user } = await supabase.auth.getUser();
          const items = get().items.map(item =>
            item.id === id ? { ...item, quantity } : item
          );
          
          set({ items });

          if (user) {
            await supabase
              .from('carts')
              .upsert({ user_id: user.id, items });
          }
        },

        clearCart: async () => {
          if (!window.confirm('Voulez-vous vraiment vider votre panier ?')) {
            return;
          }

          const { user } = await supabase.auth.getUser();
          set({ items: [], isLoading: true });

          if (user) {
            await supabase.from('carts')
              .upsert({ 
                user_id: user.id, 
                items: [] 
              });
          }

          set({ isLoading: false });
        },

        initRealtimeSync: (userId: string) => {
          const channel = supabase.channel(`cart-${userId}`)
            .on(
              'postgres_changes',
              {
                event: 'UPDATE',
                schema: 'public',
                table: 'carts',
                filter: `user_id=eq.${userId}`
              },
              (payload) => {
                if (payload.new.items) {
                  set({ items: payload.new.items });
                }
              }
            )
            .subscribe();

          return () => {
            channel.unsubscribe();
          };
        },

        undoLastRemoval: () => {
          if (lastRemovedItem) {
            get().addItem(lastRemovedItem);
            lastRemovedItem = null;
          }
        },

        applyPromoCode: (code: string) => {
          const promo = PROMO_CODES[code];
          if (!promo) {
            throw new Error('Code promo invalide');
          }

          const { subtotal } = get().getTotal();
          if (promo.minAmount && subtotal < promo.minAmount) {
            throw new Error(`Minimum ${promo.minAmount}€ d'achat requis`);
          }

          set({ promoCode: promo });
        },

        removePromoCode: () => set({ promoCode: null }),

        getTotal: () => {
          const items = get().items;
          const subtotal = items.reduce((sum, item) => sum + item.price * item.quantity, 0);
          const promo = get().promoCode;
          const discount = promo ? (subtotal * promo.discount) / 100 : 0;
          
          return {
            subtotal,
            discount,
            total: subtotal - discount
          };
        }
      };
    },
    { name: 'cart-storage' }
  )
);
