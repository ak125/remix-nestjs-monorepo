import { useState } from "react";
import { Link } from "@remix-run/react";
import { useCartStore } from "~/stores/cart.store";
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
  SheetFooter,
} from "~/components/ui/sheet";
import { Button } from "~/components/ui/button";
import { ShoppingCart, X } from "lucide-react";
import { formatCurrency } from "~/lib/format";
import { useSiteConfig } from "~/hooks/use-site-config";

export function QuickCart() {
  const { config } = useSiteConfig();
  const items = useCartStore(state => state.items);
  const [open, setOpen] = useState(false);

  const totalItems = items.length;
  const subtotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);

  return (
    <Sheet open={open} onOpenChange={setOpen}>
      <SheetTrigger asChild>
        <Button variant="ghost" size="icon" className="relative">
          <ShoppingCart className="h-5 w-5" />
          {totalItems > 0 && (
            <span className="absolute -top-1 -right-1 bg-primary text-primary-foreground rounded-full text-xs w-4 h-4 flex items-center justify-center">
              {totalItems}
            </span>
          )}
          <span className="sr-only">Panier</span>
        </Button>
      </SheetTrigger>
      <SheetContent side="right" className="sm:max-w-lg w-full">
        <SheetHeader>
          <SheetTitle>Mon Panier</SheetTitle>
        </SheetHeader>

        <div className="mt-8 space-y-4">
          {items.length === 0 ? (
            <p className="text-center text-muted-foreground py-8">
              Votre panier est vide
            </p>
          ) : (
            <>
              {items.map(item => (
                <div 
                  key={item.id} 
                  className="flex gap-4 border-b pb-4"
                >
                  {item.image && (
                    <img
                      src={item.image}
                      alt={item.name}
                      className="h-16 w-16 rounded-md object-cover"
                    />
                  )}
                  <div className="flex-1">
                    <p className="font-medium">{item.name}</p>
                    <p className="text-sm text-muted-foreground">
                      {item.quantity} x {formatCurrency(item.price)}
                    </p>
                  </div>
                  <p className="font-medium">
                    {formatCurrency(item.price * item.quantity)}
                  </p>
                </div>
              ))}

              <div className="py-4">
                <div className="flex justify-between font-medium">
                  <p>Sous-total</p>
                  <p>{formatCurrency(subtotal)}</p>
                </div>
              </div>
            </>
          )}
        </div>

        <SheetFooter className="mt-6 flex flex-col gap-3 sm:flex-row">
          <Button
            variant="outline"
            className="w-full"
            onClick={() => setOpen(false)}
          >
            Continuer mes achats
          </Button>
          <Button
            className="w-full"
            asChild
            disabled={items.length === 0}
          >
            <Link to="/panier">Valider ma commande</Link>
          </Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
