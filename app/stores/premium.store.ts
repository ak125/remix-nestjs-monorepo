import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface PremiumState {
  isVIP: boolean;
  benefits: any[];
  selectedStore: string | null;
  comparedProducts: string[];
  setVIPStatus: (status: boolean, benefits?: any[]) => void;
  selectStore: (storeId: string | null) => void;
  addToCompare: (productId: string) => void;
  removeFromCompare: (productId: string) => void;
  clearCompare: () => void;
}

export const usePremiumStore = create<PremiumState>()(
  persist(
    (set) => ({
      isVIP: false,
      benefits: [],
      selectedStore: null,
      comparedProducts: [],

      setVIPStatus: (status, benefits = []) => 
        set({ isVIP: status, benefits }),

      selectStore: (storeId) => 
        set({ selectedStore: storeId }),

      addToCompare: (productId) =>
        set((state) => ({
          comparedProducts: state.comparedProducts.length < 3 
            ? [...state.comparedProducts, productId]
            : state.comparedProducts
        })),

      removeFromCompare: (productId) =>
        set((state) => ({
          comparedProducts: state.comparedProducts.filter(id => id !== productId)
        })),

      clearCompare: () => 
        set({ comparedProducts: [] })
    }),
    { name: 'premium-features' }
  )
);
