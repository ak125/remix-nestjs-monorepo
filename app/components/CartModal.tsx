import { useEffect, useState } from "react";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { useToast } from "@/components/ui/toast";
import { LoadingSpinner } from "@/components/ui/loading";
import { formatPrice } from "@/lib/format";

interface CartItem {
  piece: {
    id: number;
    name: string;
    price: number;
    priceHT: number;
  };
  quantity: number;
  consigne?: number;
}

interface CartModalProps {
  isOpen: boolean;
  onClose: () => void;
  sessionId: string;
}

const CartModal: React.FC<CartModalProps> = ({ isOpen, onClose, sessionId }) => {
  const [loading, setLoading] = useState(false);
  const [items, setItems] = useState<CartItem[]>([]);
  const { toast } = useToast();

  useEffect(() => {
    if (isOpen && sessionId) {
      loadCart();
    }
  }, [isOpen, sessionId]);

  const loadCart = async () => {
    try {
      setLoading(true);
      const res = await fetch(`/api/cart/${sessionId}`);
      if (!res.ok) throw new Error('Erreur chargement panier');
      const data = await res.json();
      setItems(data.items || []);
    } catch (error) {
      toast({
        title: "Erreur",
        description: "Impossible de charger le panier",
        variant: "error",
      });
    } finally {
      setLoading(false);
    }
  };

  const updateCart = async (pieceId: number, action: 'plus' | 'minus' | 'drop') => {
    try {
      setLoading(true);
      const res = await fetch(`/api/cart/update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pieceId, action, sessionId }),
      });

      if (!res.ok) throw new Error('Erreur mise à jour');

      const { items: newItems } = await res.json();
      setItems(newItems);

      toast({
        title: "Succès",
        description: "Panier mis à jour",
        variant: "success",
      });
    } catch (error) {
      toast({
        title: "Erreur",
        description: "Impossible de mettre à jour le panier",
        variant: "error",
      });
    } finally {
      setLoading(false);
    }
  };

  const getTotals = () => {
    return items.reduce((acc, item) => ({
      totalHT: acc.totalHT + (item.piece.priceHT * item.quantity),
      totalTTC: acc.totalTTC + (item.piece.price * item.quantity),
      consigne: acc.consigne + ((item.consigne || 0) * item.quantity),
    }), { totalHT: 0, totalTTC: 0, consigne: 0 });
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-lg">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <span className="text-2xl">🛒</span>
            <span>Mon Panier ({items.length} articles)</span>
          </DialogTitle>
        </DialogHeader>

        <div className="p-4 min-h-[400px]">
          {loading ? (
            <div className="flex justify-center items-center h-full">
              <LoadingSpinner />
            </div>
          ) : items.length > 0 ? (
            <div className="space-y-4">
              {items.map((item) => (
                <div key={item.piece.id} className="flex items-center justify-between p-4 border rounded-lg">
                  <div className="flex-1">
                    <p className="font-medium">{item.piece.name}</p>
                    <p className="text-sm text-gray-500">
                      {formatPrice(item.piece.priceHT)} HT
                    </p>
                  </div>

                  <div className="flex items-center gap-2">
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => updateCart(item.piece.id, 'minus')}
                      disabled={loading || item.quantity <= 1}
                    >
                      -
                    </Button>
                    <span className="min-w-[2rem] text-center">{item.quantity}</span>
                    <Button
                      size="sm"
                      variant="outline"
                      onClick={() => updateCart(item.piece.id, 'plus')}
                      disabled={loading}
                    >
                      +
                    </Button>
                    <Button
                      size="sm"
                      variant="ghost"
                      className="text-red-500 hover:text-red-700"
                      onClick={() => updateCart(item.piece.id, 'drop')}
                      disabled={loading}
                    >
                      ×
                    </Button>
                  </div>

                  <div className="ml-4 text-right min-w-[80px]">
                    <p className="font-bold">{formatPrice(item.piece.price * item.quantity)}</p>
                  </div>
                </div>
              ))}

              <div className="pt-4 border-t space-y-2">
                {/* Totaux */}
                <div className="flex justify-between text-sm">
                  <span>Total HT:</span>
                  <span>{formatPrice(getTotals().totalHT)}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span>TVA:</span>
                  <span>{formatPrice(getTotals().totalTTC - getTotals().totalHT)}</span>
                </div>
                {getTotals().consigne > 0 && (
                  <div className="flex justify-between text-sm">
                    <span>Consigne:</span>
                    <span>{formatPrice(getTotals().consigne)}</span>
                  </div>
                )}
                <div className="flex justify-between font-bold">
                  <span>Total TTC:</span>
                  <span>{formatPrice(getTotals().totalTTC)}</span>
                </div>
              </div>
            </div>
          ) : (
            <p className="text-center text-gray-500">Votre panier est vide</p>
          )}
        </div>

        <DialogFooter className="space-x-2">
          <Button 
            variant="outline" 
            onClick={onClose}
            disabled={loading}
          >
            Continuer mes achats
          </Button>
          <Button
            variant="default"
            onClick={() => window.location.href = "/checkout"}
            disabled={loading || !items.length}
          >
            Valider ma commande
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
};

export default CartModal;
