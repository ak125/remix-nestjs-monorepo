import { Injectable, NotFoundException, ForbiddenException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { z } from 'zod';

const suspendAdminSchema = z.object({
  targetId: z.string(),
  actorId: z.string(),
  reason: z.string().min(1)
});

@Injectable()
export class AdminSuspensionService {
  constructor(
    private prisma: PrismaService,
    private eventEmitter: EventEmitter2
  ) {}

  async suspendAdmin(data: unknown) {
    const validated = suspendAdminSchema.parse(data);

    // Prevent self-suspension
    if (validated.targetId === validated.actorId) {
      throw new ForbiddenException('Cannot suspend yourself');
    }

    return this.prisma.$transaction(async (tx) => {
      // Check if target exists and is active
      const target = await tx.admin.findUnique({
        where: { id: validated.targetId }
      });

      if (!target) {
        throw new NotFoundException('Admin not found');
      }

      if (!target.isActive) {
        throw new ForbiddenException('Admin already suspended');
      }

      // Update admin status
      const suspended = await tx.admin.update({
        where: { id: validated.targetId },
        data: {
          isActive: false,
          history: {
            create: {
              action: 'suspended',
              details: { reason: validated.reason },
              userId: validated.actorId
            }
          }
        }
      });

      // Log action
      await tx.actionLog.create({
        data: {
          type: 'admin_suspended',
          details: {
            targetId: validated.targetId,
            reason: validated.reason
          },
          userId: validated.actorId
        }
      });

      // Create notification
      await tx.notification.create({
        data: {
          type: 'admin_suspended',
          title: 'Compte suspendu',
          message: `Le compte ${target.email} a été suspendu`,
          userId: validated.targetId
        }
      });

      // Emit event
      this.eventEmitter.emit('admin.suspended', {
        admin: suspended,
        reason: validated.reason,
        actorId: validated.actorId
      });

      return suspended;
    });
  }
}
