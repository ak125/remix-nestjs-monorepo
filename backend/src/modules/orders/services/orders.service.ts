import {
  Injectable,
  Logger,
  NotFoundException,
  BadRequestException,
} from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { OrderGateway } from '../gateways/order.gateway';
import { LoggerService } from '../../../common/services/logger.service';
import { EmailService } from '../../shared/mailer/email.service';

const ORDER_STATUS = {
  PENDING: 1,
  CANCELLED: 2,
  ORDERED: 3,
  SHIPPED: 4,
  DELIVERED: 5,
  EQUIVALENCE_PROPOSED: 91,
} as const;

@Injectable()
export class OrdersService {
  constructor(
    private readonly prisma: PrismaService,
    private readonly eventEmitter: EventEmitter2,
    private readonly orderGateway: OrderGateway,
    private readonly logger: LoggerService,
    private readonly emailService: EmailService,
  ) {}

  async getOrderLine(orderId: string, lineId: number, sessionId: string) {
    const orderLine = await this.prisma.xTR_ORDER_LINE.findFirst({
      where: {
        ORL_ID: lineId,
        ORL_ORD_ID: orderId,
      },
      include: {
        xTR_PIECE: true,
        xTR_SUPPLIER: true,
      },
    });

    if (!orderLine) {
      throw new NotFoundException('Ligne introuvable');
    }

    return orderLine;
  }

  async updateOrderLine(
    orderId: string,
    lineId: number,
    data: {
      supplierId?: number;
      priceHT?: number;
      quantity?: number;
    },
    sessionId: string,
  ) {
    try {
      // Transaction pour mise à jour et log
      const [updatedLine, logEntry] = await this.prisma.$transaction([
        // Mise à jour ligne
        this.prisma.xTR_ORDER_LINE.update({
          where: {
            ORL_ID: lineId,
            ORL_ORD_ID: orderId,
          },
          data: {
            ORL_SPL_ID: data.supplierId,
            ORL_SPL_PRICE_BUY_UNIT_HT: data.priceHT,
            ORL_ART_QUANTITY: data.quantity,
            ORL_ORLS_ID: ORDER_STATUS.ORDERED,
          },
          include: {
            xTR_PIECE: true,
            xTR_SUPPLIER: true,
          },
        }),

        // Création log
        this.prisma.xTR_ORDER_LOG.create({
          data: {
            order_id: orderId,
            line_id: lineId,
            log_date: new Date(),
            log_message: `Mise à jour: ${data.supplierId ? 'Fournisseur' : ''} ${data.priceHT ? 'Prix' : ''} ${data.quantity ? 'Quantité' : ''}`,
          },
        }),
      ]);

      // Notification temps réel
      this.orderGateway.notifyOrderUpdate(
        {
          orderId,
          lineId,
          previousStatus: updatedLine.ORL_ORLS_ID,
          newStatus: ORDER_STATUS.ORDERED,
          piece: {
            id: updatedLine.piece_id,
            name: updatedLine.xTR_PIECE.piece_name,
          },
        },
        sessionId,
      );

      return {
        success: true,
        message: 'Ligne mise à jour avec succès',
        orderLine: updatedLine,
        log: logEntry,
      };
    } catch (error) {
      this.logger.error('Erreur mise à jour:', error);
      throw new Error('Erreur lors de la mise à jour');
    }
  }

  async proposeEquivalence(
    orderId: string,
    lineId: number,
    data: {
      pieceId: string;
      quantity: number;
      priceHT: number;
    },
    sessionId: string,
  ) {
    const { pieceId, quantity, priceHT } = data;

    try {
      // Transaction pour l'équivalence
      const [newLine, updatedOriginal, logEntry] =
        await this.prisma.$transaction([
          // Créer nouvelle ligne
          this.prisma.xTR_ORDER_LINE.create({
            data: {
              ORL_ORD_ID: orderId,
              piece_id: pieceId,
              ORL_ART_QUANTITY: quantity,
              ORL_SPL_PRICE_BUY_UNIT_HT: priceHT,
              ORL_ORLS_ID: ORDER_STATUS.EQUIVALENCE_PROPOSED,
            },
            include: {
              xTR_PIECE: true,
            },
          }),

          // Mettre à jour ligne originale
          this.prisma.xTR_ORDER_LINE.update({
            where: {
              ORL_ID: lineId,
              ORL_ORD_ID: orderId,
            },
            data: {
              ORL_EQUIV_ID: lineId, // Sera mis à jour avec l'ID de la nouvelle ligne
            },
          }),

          // Créer log
          this.prisma.xTR_ORDER_LOG.create({
            data: {
              order_id: orderId,
              line_id: lineId,
              log_date: new Date(),
              log_message: `Proposition d'équivalence créée`,
            },
          }),
        ]);

      // Notification
      this.eventEmitter.emit('order.equivalence.proposed', {
        orderId,
        originalLineId: lineId,
        newLineId: newLine.ORL_ID,
        timestamp: new Date(),
      });

      return {
        success: true,
        message: 'Équivalence proposée avec succès',
        newLine,
        logEntry,
      };
    } catch (error) {
      this.logger.error('Erreur équivalence:', error);
      throw new Error('Erreur lors de la proposition');
    }
  }

  async updateOrderStatus(
    orderId: string,
    lineId: number,
    statusId: number,
    sessionId: string,
  ) {
    try {
      // Transaction pour mise à jour et log
      const [updatedLine, logEntry] = await this.prisma.$transaction([
        // Mise à jour ligne
        this.prisma.xTR_ORDER_LINE.update({
          where: {
            ORL_ID: lineId,
            ORL_ORD_ID: orderId,
          },
          data: {
            ORL_ORLS_ID: statusId,
          },
          include: {
            xTR_PIECE: true,
          },
        }),

        // Création log
        this.prisma.xTR_ORDER_LOG.create({
          data: {
            order_id: orderId,
            line_id: lineId,
            status_old: updatedLine?.ORL_ORLS_ID || 0,
            status_new: statusId,
            log_date: new Date(),
            log_message: `Mise à jour statut: ${statusId}`,
          },
        }),
      ]);

      this.logger.log('Statut mis à jour', 'OrderService', {
        orderId,
        lineId,
        oldStatus: updatedLine.ORL_ORLS_ID,
        newStatus: statusId,
      });

      // Notification WebSocket
      this.orderGateway.notifyOrderUpdate(
        {
          orderId,
          lineId,
          previousStatus: updatedLine.ORL_ORLS_ID,
          newStatus: statusId,
          piece: {
            name: updatedLine.xTR_PIECE.piece_name,
            ref: updatedLine.xTR_PIECE.piece_ref,
          },
        },
        sessionId,
      );

      return {
        success: true,
        orderLine: updatedLine,
        log: logEntry,
      };
    } catch (error) {
      this.logger.error('Erreur mise à jour statut', 'OrderService', error);
      throw new Error('Erreur lors de la mise à jour du statut');
    }
  }

  async isUserAllowedForOrder(
    sessionId: string,
    orderId: string,
  ): Promise<boolean> {
    const order = await this.prisma.xTR_ORDER.findFirst({
      where: {
        order_id: orderId,
      },
      include: {
        xTR_CUSTOMER: true,
      },
    });

    if (!order) return false;

    // Vérifier la session avec le customer_id de la commande
    const sessionData = await this.prisma.xTR_SESSION.findFirst({
      where: {
        session_id: sessionId,
        customer_id: order.xTR_CUSTOMER.CST_ID,
      },
    });

    return !!sessionData;
  }

  async cancelOrder(orderId: number, userId: number) {
    try {
      // Vérifier la commande
      const order = await this.prisma.order.findUnique({
        where: { id: orderId },
        include: {
          customer: true,
          orderLines: true,
        },
      });

      if (!order) {
        throw new NotFoundException('Commande non trouvée');
      }

      if (order.customerId !== userId) {
        throw new BadRequestException('Non autorisé');
      }

      if (order.status === 'CANCELED') {
        throw new BadRequestException('Commande déjà annulée');
      }

      if (['SHIPPED', 'DELIVERED'].includes(order.status)) {
        throw new BadRequestException('Commande ne peut plus être annulée');
      }

      // Transaction d'annulation
      const [updatedOrder, refundTicket] = await this.prisma.$transaction([
        // Mise à jour commande
        this.prisma.order.update({
          where: { id: orderId },
          data: { 
            status: 'CANCELED',
            orderLines: {
              updateMany: {
                where: { orderId },
                data: { status: 'CANCELED' }
              }
            }
          },
          include: {
            customer: true,
            orderLines: true,
          }
        }),

        // Création ticket remboursement
        this.prisma.refundTicket.create({
          data: {
            orderId,
            amount: order.orderLines.reduce(
              (sum, line) => sum + line.priceTTC,
              0
            ),
          }
        })
      ]);

      // Notifications
      this.eventEmitter.emit('order.canceled', {
        order: updatedOrder,
        refundTicket,
      });

      // Email
      await this.emailService.sendOrderCancellationEmail({
        to: order.customer.email,
        orderNumber: orderId,
        refundAmount: refundTicket.amount,
      });

      return {
        message: 'Commande annulée avec succès',
        order: updatedOrder,
        refundTicket,
      };

    } catch (error) {
      this.logger.error('Erreur annulation commande', 'Orders', {
        error,
        orderId,
        userId,
      });
      throw error;
    }
  }

  async getOrderDetails(orderId: number, userId: number) {
    const order = await this.prisma.order.findUnique({
      where: { id: orderId },
      include: {
        customer: true,
        billingAddress: true,
        shippingAddress: true,
        orderLines: {
          include: {
            piece: true,
          },
        },
      },
    });

    if (!order) {
      throw new NotFoundException('Commande non trouvée');
    }

    if (order.customerId !== userId) {
      throw new BadRequestException('Non autorisé');
    }

    return order;
  }

  private async notifyOrderCanceled(order: any, refundTicket: any) {
    // Email client
    await this.emailService.sendOrderCancellationEmail({
      to: order.customer.email,
      orderNumber: order.id,
      refundAmount: refundTicket.amount,
    });

    // Event pour WebSocket
    this.eventEmitter.emit('order.canceled', {
      orderId: order.id,
      customerId: order.customerId,
      amount: refundTicket.amount,
    });

    // Log
    this.logger.info('Commande annulée', 'Orders', {
      orderId: order.id,
      refundTicketId: refundTicket.id,
    });
  }

  async getOrdersByCustomer(customerId: number) {
    try {
      const orders = await this.prisma.order.findMany({
        where: { customerId },
        include: {
          orderLines: {
            include: {
              piece: true,
            },
          },
          billingAddress: true,
          shippingAddress: true,
        },
        orderBy: {
          createdAt: 'desc',
        },
      });

      return orders.map(order => ({
        ...order,
        totalAmount: this.calculateOrderTotal(order),
        totalHT: this.calculateOrderTotalHT(order),
      }));

    } catch (error) {
      this.logger.error('Erreur récupération commandes', 'Orders', {
        error,
        customerId,
      });
      throw error;
    }
  }

  private calculateOrderTotal(order: any): number {
    return order.orderLines.reduce(
      (sum: number, line: any) => 
        sum + (line.priceTTC * line.quantity) + (line.consigneTTC || 0),
      0
    );
  }

  private calculateOrderTotalHT(order: any): number {
    return order.orderLines.reduce(
      (sum: number, line: any) => 
        sum + (line.priceHT * line.quantity) + (line.consigneHT || 0),
      0
    );
  }
}
