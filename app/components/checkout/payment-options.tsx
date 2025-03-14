import { motion } from 'framer-motion';
import { useCheckoutStore } from '~/stores/checkout.store';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "~/components/ui/select";
import { Switch } from "~/components/ui/switch";
import { Label } from "~/components/ui/label";

interface PaymentOptionsProps {
  loyaltyPoints: number;
  pointsValue: number;
  total: number;
}

export function PaymentOptions({ loyaltyPoints, pointsValue, total }: PaymentOptionsProps) {
  const {
    paymentMethod,
    installments,
    usePoints,
    setPaymentMethod,
    setInstallments,
    toggleUsePoints
  } = useCheckoutStore();

  const monthlyPayment = (total / installments).toFixed(2);

  return (
    <div className="space-y-6">
      <div className="space-y-2">
        <Label>Mode de paiement</Label>
        <Select
          value={paymentMethod}
          onValueChange={(value: 'immediate' | 'installments') => 
            setPaymentMethod(value)
          }
        >
          <SelectTrigger>
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="immediate">Paiement immédiat</SelectItem>
            <SelectItem value="installments">Paiement en plusieurs fois</SelectItem>
          </SelectContent>
        </Select>
      </div>

      {paymentMethod === 'installments' && (
        <motion.div
          initial={{ height: 0, opacity: 0 }}
          animate={{ height: 'auto', opacity: 1 }}
          className="space-y-2"
        >
          <Label>Nombre de mensualités</Label>
          <Select
            value={String(installments)}
            onValueChange={(value) => setInstallments(Number(value))}
          >
            <SelectTrigger>
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="3">3x sans frais ({monthlyPayment}€/mois)</SelectItem>
              <SelectItem value="4">4x sans frais ({monthlyPayment}€/mois)</SelectItem>
            </SelectContent>
          </Select>
        </motion.div>
      )}

      {loyaltyPoints > 0 && (
        <div className="flex items-center justify-between">
          <div className="space-y-1">
            <Label>Utiliser mes points</Label>
            <p className="text-sm text-gray-500">
              Vous avez {loyaltyPoints} points ({pointsValue}€)
            </p>
          </div>
          <Switch
            checked={usePoints}
            onCheckedChange={toggleUsePoints}
          />
        </div>
      )}
    </div>
  );
}
