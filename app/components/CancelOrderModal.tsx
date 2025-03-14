import * as React from "react";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { useToast } from "@/components/ui/toast";
import { LoadingSpinner } from "@/components/ui/loading";

import { useState } from "react";

interface CancelOrderModalProps {
  isOpen: boolean;
  onClose: () => void;
  orderId: number;
  onCanceled?: () => void;
}

export default function CancelOrderModal({ 
  isOpen, 
  onClose, 
  orderId,
  onCanceled 
}: CancelOrderModalProps) {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const { toast } = useToast();

  const handleCancel = async () => {
    setLoading(true);
    setError(null);

    try {
      const res = await fetch(`/api/orders/${orderId}/cancel`, {
        method: 'PATCH',
        credentials: 'include',
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.message || 'Erreur lors de l\'annulation');
      }

      toast({
        title: "Commande annulée",
        description: "Un email de confirmation vous a été envoyé",
        variant: "success",
      });

      onCanceled?.();
      onClose();

    } catch (err) {
      setError(err instanceof Error ? err.message : 'Erreur inconnue');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Dialog open={isOpen} onOpenChange={onClose}>
      <DialogContent className="max-w-md">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <span className="text-2xl">⚠️</span>
            <span>Annulation de la commande #{orderId}</span>
          </DialogTitle>
        </DialogHeader>

        <div className="p-4 space-y-4">
          {error ? (
            <Alert variant="destructive">
              <AlertDescription>{error}</AlertDescription>
            </Alert>
          ) : (
            <p className="text-center text-gray-600">
              Êtes-vous sûr de vouloir annuler cette commande ?
              Cette action est irréversible.
            </p>
          )}
        </div>

        <DialogFooter className="gap-2">
          <Button
            variant="outline"
            onClick={onClose}
            disabled={loading}
          >
            Retour
          </Button>
          <Button
            variant="destructive"
            onClick={handleCancel}
            disabled={loading}
          >
            {loading ? (
              <LoadingSpinner className="mr-2" />
            ) : (
              "Confirmer l'annulation"
            )}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
