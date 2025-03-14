import { PayPalButtons } from "@paypal/react-paypal-js";
import { useToast } from "@/components/ui/toast";
import { useState } from "react";

interface PayPalButtonProps {
  amount: number;
  orderId: string;
  onSuccess?: () => void;
  onError?: (error: Error) => void;
}

export default function PayPalButton({
  amount,
  orderId,
  onSuccess,
  onError,
}: PayPalButtonProps) {
  const [loading, setLoading] = useState(false);
  const { toast } = useToast();

  const createOrder = async () => {
    try {
      setLoading(true);
      const response = await fetch("/api/payments/paypal/create-order", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ amount, orderId }),
      });

      if (!response.ok) throw new Error("Erreur création paiement");

      const data = await response.json();
      return data.id;

    } catch (error) {
      toast({
        title: "Erreur",
        description: "Impossible d'initialiser le paiement",
        variant: "destructive",
      });
      onError?.(error as Error);
      throw error;
    } finally {
      setLoading(false);
    }
  };

  const onApprove = async (data: any) => {
    try {
      setLoading(true);
      const response = await fetch("/api/payments/paypal/capture", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ paypalOrderId: data.orderID }),
      });

      if (!response.ok) throw new Error("Erreur capture paiement");

      toast({
        title: "Succès",
        description: "Paiement validé",
      });
      
      onSuccess?.();

    } catch (error) {
      toast({
        title: "Erreur",
        description: "Échec de la validation du paiement",
        variant: "destructive",
      });
      onError?.(error as Error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className={loading ? "opacity-50 pointer-events-none" : ""}>
      <PayPalButtons
        createOrder={createOrder}
        onApprove={onApprove}
        style={{ layout: "horizontal" }}
      />
    </div>
  );
}
