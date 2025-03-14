import { z } from 'zod';

export const CartItemSchema = z.object({
  id: z.string().min(1, 'ID requis'),
  quantity: z.number().min(1, 'Quantité minimale: 1'),
  priceHT: z.number().positive('Prix HT invalide'),
  priceTTC: z.number().positive('Prix TTC invalide'),
  consigneHT: z.number().min(0, 'Consigne HT invalide').default(0),
  consigneTTC: z.number().min(0, 'Consigne TTC invalide').default(0),
  type: z.enum(['piece', 'manuscript', 'copy']).default('piece'),
  format: z.enum(['A4', 'A3']).optional(),
  recto_verso: z.boolean().optional(),
});

export type CartItem = z.infer<typeof CartItemSchema>;

export interface CartData {
  items: Record<string, CartItem>;
  totalAmount: number;
  totalConsigne: number;
  updatedAt: Date;
}
