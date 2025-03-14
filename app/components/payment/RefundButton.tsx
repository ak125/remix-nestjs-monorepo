import { useState } from "react";
import { Button } from "@/components/ui/button";
import { LoadingSpinner } from "@/components/ui/loading";
import { useToast } from "@/components/ui/toast";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";

interface RefundButtonProps {
  orderId: string;
  orderAmount: number;
  onRefunded?: () => void;
}

export default function RefundButton({ 
  orderId, 
  orderAmount,
  onRefunded,
}: RefundButtonProps) {
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const [amount, setAmount] = useState<number | ''>('');
  const { toast } = useToast();

  const handleRefund = async () => {
    try {
      setLoading(true);
      const response = await fetch("/api/payments/refund", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
          orderId,
          amount: amount || undefined,
        }),
      });

      if (!response.ok) throw new Error("Échec du remboursement");

      toast({
        title: "Remboursement effectué",
        description: `Montant: ${amount || orderAmount}€`,
      });

      onRefunded?.();
      setOpen(false);

    } catch (error) {
      toast({
        title: "Erreur",
        description: "Le remboursement a échoué",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <>
      <Button
        variant="outline"
        size="sm"
        onClick={() => setOpen(true)}
      >
        Rembourser
      </Button>

      <Dialog open={open} onOpenChange={setOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Rembourser la commande #{orderId}</DialogTitle>
          </DialogHeader>

          <div className="space-y-4 py-4">
            <div className="space-y-2">
              <label className="text-sm font-medium">
                Montant (laisser vide pour rembourser tout)
              </label>
              <Input
                type="number"
                step="0.01"
                max={orderAmount}
                value={amount}
                onChange={(e) => setAmount(Number(e.target.value) || '')}
              />
            </div>
          </div>

          <div className="flex justify-end gap-2">
            <Button
              variant="outline"
              onClick={() => setOpen(false)}
              disabled={loading}
            >
              Annuler
            </Button>
            <Button
              onClick={handleRefund}
              disabled={loading}
            >
              {loading ? (
                <LoadingSpinner className="mr-2" />
              ) : (
                "Confirmer"
              )}
            </Button>
          </div>
        </DialogContent>
      </Dialog>
    </>
  );
}
