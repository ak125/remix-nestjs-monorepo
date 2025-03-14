import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface CartExtrasState {
  giftCards: {
    code: string;
    amount: number;
  }[];
  recommendations: any[];
  addGiftCard: (code: string, amount: number) => void;
  removeGiftCard: (code: string) => void;
  setRecommendations: (items: any[]) => void;
  getGiftCardTotal: () => number;
}

export const useCartExtrasStore = create<CartExtrasState>()(
  persist(
    (set, get) => ({
      giftCards: [],
      recommendations: [],

      addGiftCard: (code, amount) => 
        set(state => ({
          giftCards: [...state.giftCards, { code, amount }]
        })),

      removeGiftCard: (code) =>
        set(state => ({
          giftCards: state.giftCards.filter(card => card.code !== code)
        })),

      setRecommendations: (items) =>
        set({ recommendations: items }),

      getGiftCardTotal: () =>
        get().giftCards.reduce((sum, card) => sum + card.amount, 0)
    }),
    { name: 'cart-extras' }
  )
);
