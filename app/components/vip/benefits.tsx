import { motion } from 'framer-motion';
import { usePremiumStore } from '~/stores/premium.store';
import { Button } from '~/components/ui/button';
import { Crown, Truck, CreditCard, Clock } from 'lucide-react';
import { cn } from '~/lib/utils';

const benefitIcons = {
  shipping: Truck,
  cashback: CreditCard,
  early_access: Clock
};

export function VIPBenefits() {
  const { isVIP, benefits } = usePremiumStore();

  if (!isVIP) return null;

  return (
    <div className="rounded-lg bg-gradient-to-r from-amber-100 to-amber-50 p-6">
      <div className="flex items-center gap-2 mb-4">
        <Crown className="h-5 w-5 text-amber-600" />
        <h2 className="font-medium">Vos avantages VIP</h2>
      </div>

      <div className="grid gap-4 md:grid-cols-3">
        {benefits.map((benefit, index) => {
          const Icon = benefitIcons[benefit.type as keyof typeof benefitIcons];
          
          return (
            <motion.div
              key={benefit.type}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.1 }}
              className="bg-white/50 backdrop-blur rounded-md p-4"
            >
              <Icon className="h-8 w-8 text-amber-600 mb-2" />
              <p className="font-medium">{benefit.description}</p>
            </motion.div>
          );
        })}
      </div>
    </div>
  );
}
