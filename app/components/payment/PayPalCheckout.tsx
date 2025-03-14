import { useState } from "react";
import { PayPalButtons, FUNDING } from "@paypal/react-paypal-js";
import { useNavigate } from "@remix-run/react";
import { useToast } from "@/components/ui/toast";

interface PayPalCheckoutProps {
  amount: number;
  orderId: string;
  onSuccess?: () => void;
  onError?: (error: Error) => void;
}

export default function PayPalCheckout({
  amount,
  orderId,
  onSuccess,
  onError,
}: PayPalCheckoutProps) {
  const [processing, setProcessing] = useState(false);
  const navigate = useNavigate();
  const { toast } = useToast();

  const createOrder = async (method?: 'PAY_LATER') => {
    try {
      setProcessing(true);
      const response = await fetch("/api/payments/paypal/create-order", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
          amount, 
          orderId,
          paymentMethod: method,
        }),
      });

      if (!response.ok) throw new Error("Erreur création paiement");

      const data = await response.json();
      return data.id;

    } catch (error) {
      onError?.(error as Error);
      toast({
        title: "Erreur",
        description: "Impossible d'initialiser le paiement",
        variant: "destructive",
      });
      throw error;
    } finally {
      setProcessing(false);
    }
  };

  const handleApprove = async (data: any) => {
    try {
      setProcessing(true);
      const response = await fetch("/api/payments/paypal/capture", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ paypalOrderId: data.orderID }),
      });

      if (!response.ok) throw new Error("Erreur validation paiement");

      toast({
        title: "Paiement validé",
        description: "Votre commande a été confirmée",
      });

      onSuccess?.();
      navigate(`/payment/confirmation?orderId=${orderId}`);

    } catch (error) {
      onError?.(error as Error);
      toast({
        title: "Erreur",
        description: "Échec de la validation du paiement",
        variant: "destructive",
      });
    } finally {
      setProcessing(false);
    }
  };

  return (
    <div className="space-y-6">
      <div className={processing ? "opacity-50 pointer-events-none" : ""}>
        <h2 className="font-medium mb-2">Paiement immédiat</h2>
        <PayPalButtons 
          createOrder={() => createOrder()}
          onApprove={handleApprove}
        />
      </div>

      <div className={processing ? "opacity-50 pointer-events-none" : ""}>
        <h2 className="font-medium mb-2">Payer en plusieurs fois</h2>
        <PayPalButtons
          createOrder={() => createOrder('PAY_LATER')}
          onApprove={handleApprove}
          fundingSource={FUNDING.PAY_LATER}
        />
      </div>
    </div>
  );
}
