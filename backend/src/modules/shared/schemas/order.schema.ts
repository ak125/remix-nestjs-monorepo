import { z } from 'zod';

export const ORDER_STATUS = {
  PENDING: 1,
  CANCELLED: 2,
  ORDERED: 3,
  SHIPPED: 4,
  DELIVERED: 5,
  EQUIVALENCE_PROPOSED: 91,
  EQUIVALENCE_ACCEPTED: 92,
  EQUIVALENCE_REJECTED: 93,
  EQUIVALENCE_VALIDATED: 94,
} as const;

export const ShippingFeeSchema = z.object({
  ordId: z.number().positive('ID commande requis'),
  shippingFee: z.number().positive('Frais de port doivent être positifs'),
  carrier: z.string().optional(),
});

export interface OrderNotification {
  orderId: string;
  lineId: number;
  previousStatus?: number;
  newStatus: number;
  piece?: {
    id?: string;
    name?: string;
    ref?: string;
  };
}

export interface ShippingConfig {
  defaultRate: number;
  freeShippingThreshold: number;
  expressRate: number;
}
