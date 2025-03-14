import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { WebSocketGateway, WebSocketServer } from '@nestjs/websockets';
import { Server } from 'socket.io';
import { z } from 'zod';

const moveStockSchema = z.object({
  type: z.enum(['in', 'out', 'adjustment']),
  quantity: z.number(),
  reason: z.string().optional(),
  userId: z.string()
});

@WebSocketGateway({
  cors: {
    origin: process.env.CLIENT_URL
  }
})
@Injectable()
export class StockMovementService {
  @WebSocketServer()
  server: Server;

  constructor(private prisma: PrismaService) {}

  async moveStock(stockId: string, data: unknown) {
    const validated = moveStockSchema.parse(data);

    return this.prisma.$transaction(async (tx) => {
      // Get current stock
      const stock = await tx.stock.findUnique({
        where: { id: stockId }
      });

      // Calculate new quantity
      const newQuantity = validated.type === 'out' 
        ? stock.quantity - validated.quantity
        : stock.quantity + validated.quantity;

      // Update stock
      const updated = await tx.stock.update({
        where: { id: stockId },
        data: { quantity: newQuantity }
      });

      // Create movement record
      const movement = await tx.stockMovement.create({
        data: {
          stockId,
          type: validated.type,
          quantity: validated.quantity,
          oldQuantity: stock.quantity,
          newQuantity,
          reason: validated.reason,
          userId: validated.userId
        }
      });

      // Create notification if low stock
      if (newQuantity <= stock.minQuantity) {
        await tx.stockNotification.create({
          data: {
            stockId,
            type: 'low_stock',
            message: `Stock niveau bas: ${stock.name} (${newQuantity} restants)`,
            userId: validated.userId
          }
        });

        // Emit notification
        this.server.emit('stockAlert', {
          type: 'low_stock',
          stock: updated
        });
      }

      return movement;
    });
  }
}
