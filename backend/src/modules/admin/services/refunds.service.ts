import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../../../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { EmailService } from '../../shared/mailer/email.service';

interface GetRefundsParams {
  status?: string;
  orderId?: string;
  page: number;
  limit: number;
}

@Injectable()
export class AdminRefundsService {
  private readonly logger = new Logger(AdminRefundsService.name);

  constructor(
    private readonly prisma: PrismaService,
    private readonly eventEmitter: EventEmitter2,
    private readonly emailService: EmailService,
  ) {}

  async getRefunds(params: GetRefundsParams) {
    const { status, orderId, page, limit } = params;

    const [refunds, total] = await Promise.all([
      this.prisma.refund.findMany({
        where: {
          status: status || undefined,
          orderId: orderId || undefined,
        },
        include: {
          payment: {
            include: {
              order: {
                include: {
                  customer: true,
                },
              },
            },
          },
        },
        orderBy: { createdAt: 'desc' },
        skip: (page - 1) * limit,
        take: limit,
      }),
      this.prisma.refund.count({
        where: {
          status: status || undefined,
          orderId: orderId || undefined,
        },
      }),
    ]);

    return {
      refunds,
      pagination: {
        total,
        pages: Math.ceil(total / limit),
        current: page,
      },
    };
  }

  async updateRefund(id: string, data: { status: string; reason?: string }) {
    const refund = await this.prisma.refund.update({
      where: { id },
      data,
      include: {
        payment: {
          include: {
            order: {
              include: {
                customer: true,
              },
            },
          },
        },
      },
    });

    // Notifications
    this.eventEmitter.emit('refund.updated', {
      refundId: id,
      status: data.status,
    });

    if (data.status === 'COMPLETED') {
      await this.emailService.sendRefundConfirmation({
        to: refund.payment.order.customer.email,
        orderId: refund.payment.orderId,
        amount: refund.amount,
      });
    }

    return refund;
  }
}
