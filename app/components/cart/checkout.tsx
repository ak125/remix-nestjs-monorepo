import { useState } from "react";
import { useFetcher } from "@remix-run/react";
import { useCartStore } from "~/stores/cart.store";
import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { Loader2 } from "lucide-react";
import { useToast } from "~/hooks/use-toast";

export function Checkout() {
  const [promoCode, setPromoCode] = useState("");
  const { items, applyPromoCode, removePromoCode, getTotal } = useCartStore();
  const fetcher = useFetcher();
  const { toast } = useToast();

  const { subtotal, discount, total } = getTotal();
  const isLoading = fetcher.state !== 'idle';

  const handlePromoCode = () => {
    try {
      applyPromoCode(promoCode);
      toast({
        title: "Code promo appliqué",
        description: `Réduction de ${discount}€ appliquée`
      });
    } catch (error) {
      toast({
        title: "Erreur",
        description: error instanceof Error ? error.message : "Code promo invalide",
        variant: "destructive"
      });
    }
  };

  return (
    <div className="space-y-4">
      <div className="flex gap-2">
        <Input
          placeholder="Code promo"
          value={promoCode}
          onChange={(e) => setPromoCode(e.target.value)}
        />
        <Button onClick={handlePromoCode} variant="outline">
          Appliquer
        </Button>
      </div>

      <div className="space-y-2 text-sm">
        <div className="flex justify-between">
          <span>Sous-total</span>
          <span>{subtotal.toFixed(2)}€</span>
        </div>
        {discount > 0 && (
          <div className="flex justify-between text-green-600">
            <span>Réduction</span>
            <span>-{discount.toFixed(2)}€</span>
          </div>
        )}
        <div className="flex justify-between font-medium text-lg">
          <span>Total</span>
          <span>{total.toFixed(2)}€</span>
        </div>
      </div>

      <fetcher.Form method="post" action="/api/checkout" className="space-y-4">
        <Button 
          type="submit"
          className="w-full"
          disabled={isLoading || items.length === 0}
        >
          {isLoading ? (
            <>
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              Redirection...
            </>
          ) : (
            "Payer maintenant"
          )}
        </Button>
      </fetcher.Form>
    </div>
  );
}
