import { create } from 'zustand';
import { persist } from 'zustand/middleware';

interface CheckoutState {
  paymentMethod: 'immediate' | 'installments';
  installments: number;
  usePoints: boolean;
  pointsToRedeem: number;
  setPaymentMethod: (method: 'immediate' | 'installments') => void;
  setInstallments: (count: number) => void;
  toggleUsePoints: () => void;
  setPointsToRedeem: (points: number) => void;
}

export const useCheckoutStore = create<CheckoutState>()(
  persist(
    (set) => ({
      paymentMethod: 'immediate',
      installments: 3,
      usePoints: false,
      pointsToRedeem: 0,

      setPaymentMethod: (method) => 
        set({ paymentMethod: method }),

      setInstallments: (count) => 
        set({ installments: count }),

      toggleUsePoints: () =>
        set((state) => ({ usePoints: !state.usePoints })),

      setPointsToRedeem: (points) =>
        set({ pointsToRedeem: points })
    }),
    { name: 'checkout-preferences' }
  )
);
