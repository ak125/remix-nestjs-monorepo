import { z } from 'zod';

// Schémas de validation
const CartItemSchema = z.object({
  id: z.string(),
  pieceId: z.string(),
  quantity: z.number().min(1),
  priceHT: z.number().positive(),
  priceTTC: z.number().positive(),
  consigneHT: z.number().default(0),
  consigneTTC: z.number().default(0),
  name: z.string(),
  ref: z.string(),
  image: z.string().optional(),
});

const CartSchema = z.object({
  id: z.string(),
  items: z.array(CartItemSchema),
  totalAmount: z.number(),
  totalConsigne: z.number(),
  updatedAt: z.string().datetime(),
});

type CartItem = z.infer<typeof CartItemSchema>;
type Cart = z.infer<typeof CartSchema>;

class CartError extends Error {
  constructor(
    message: string,
    public status?: number,
    public details?: unknown,
  ) {
    super(message);
    this.name = 'CartError';
  }
}

export const cartService = {
  async getCart(userId: string): Promise<Cart> {
    try {
      const response = await fetch(`/api/cart/${userId}`, {
        credentials: 'include',
      });

      if (!response.ok) {
        throw new CartError(`HTTP error! status: ${response.status}`, response.status);
      }

      const data = await response.json();
      const result = CartSchema.safeParse(data);

      if (!result.success) {
        throw new CartError('Invalid cart data', undefined, result.error);
      }

      return result.data;
    } catch (error) {
      throw new CartError(
        error instanceof CartError ? error.message : 'Failed to fetch cart',
        error instanceof CartError ? error.status : undefined,
        error,
      );
    }
  },

  async addToCart(userId: string, item: Partial<CartItem>): Promise<Cart> {
    try {
      const response = await fetch(`/api/cart/${userId}`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(item),
      });

      if (!response.ok) {
        throw new CartError(`HTTP error! status: ${response.status}`, response.status);
      }

      const data = await response.json();
      const result = CartSchema.safeParse(data);

      if (!result.success) {
        throw new CartError('Invalid response data', undefined, result.error);
      }

      return result.data;
    } catch (error) {
      throw new CartError(
        error instanceof CartError ? error.message : 'Failed to add item to cart',
        error instanceof CartError ? error.status : undefined,
        error,
      );
    }
  },

  async removeItem(userId: string, itemId: string): Promise<Cart> {
    try {
      const response = await fetch(`/api/cart/${userId}/${itemId}`, {
        method: 'DELETE',
        credentials: 'include',
      });

      if (!response.ok) {
        throw new CartError(`HTTP error! status: ${response.status}`, response.status);
      }

      const data = await response.json();
      const result = CartSchema.safeParse(data);

      if (!result.success) {
        throw new CartError('Invalid response data', undefined, result.error);
      }

      return result.data;
    } catch (error) {
      throw new CartError(
        error instanceof CartError ? error.message : 'Failed to remove item',
        error instanceof CartError ? error.status : undefined,
        error,
      );
    }
  },

  async updateQuantity(userId: string, itemId: string, quantity: number): Promise<Cart> {
    try {
      const response = await fetch(`/api/cart/${userId}/items/${itemId}`, {
        method: 'PATCH',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ quantity }),
      });

      if (!response.ok) {
        throw new CartError(`HTTP error! status: ${response.status}`, response.status);
      }

      const data = await response.json();
      const result = CartSchema.safeParse(data);

      if (!result.success) {
        throw new CartError('Invalid response data', undefined, result.error);
      }

      return result.data;
    } catch (error) {
      throw new CartError(
        error instanceof CartError ? error.message : 'Failed to update quantity',
        error instanceof CartError ? error.status : undefined,
        error,
      );
    }
  },
};
