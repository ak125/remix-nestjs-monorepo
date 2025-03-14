import { useEffect } from 'react';
import { useCartStore } from '~/stores/cart.store';
import { supabase } from '~/lib/supabase.server';

export function useCartSync() {
  const syncCart = useCartStore(state => state.syncCart);

  useEffect(() => {
    let cleanup: (() => void) | undefined;

    supabase.auth.getUser().then(({ data: { user } }) => {
      if (user) {
        cleanup = syncCart(user.id);
      }
    });

    return () => {
      cleanup?.();
    };
  }, [syncCart]);
}
