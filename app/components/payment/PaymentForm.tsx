import { useEffect, useRef } from "react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";

interface PaymentFormProps {
  orderId: string;
  amount: number;
  redirectUrl: string;
  formParams: Record<string, string>;
}

export function PaymentForm({ orderId, amount, redirectUrl, formParams }: PaymentFormProps) {
  const formRef = useRef<HTMLFormElement>(null);

  useEffect(() => {
    formRef.current?.submit();
  }, []);

  return (
    <Card className="max-w-md mx-auto p-6">
      <form 
        ref={formRef}
        method="POST"
        action={redirectUrl}
        className="space-y-4"
      >
        {Object.entries(formParams).map(([key, value]) => (
          <input key={key} type="hidden" name={key} value={value} />
        ))}

        <div className="text-center space-y-4">
          <h2 className="text-lg font-medium">
            Redirection vers la page de paiement...
          </h2>
          <p className="text-sm text-gray-500">
            Commande #{orderId} - {amount}€
          </p>
          <Button type="submit" className="w-full">
            Payer maintenant
          </Button>
        </div>
      </form>
    </Card>
  );
}
