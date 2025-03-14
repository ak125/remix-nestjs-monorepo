import { Injectable } from '@nestjs/common';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { LoggerService } from '../../../common/services/logger.service';
import { MailerService } from './mailer.service';

@Injectable()
export class NotificationsService {
  constructor(
    private readonly mailer: MailerService,
    private readonly eventEmitter: EventEmitter2,
    private readonly logger: LoggerService,
  ) {}

  async sendOrderNotification(notification: any) {
    try {
      // Email
      await this.mailer.sendOrderEmail(notification);

      // WebSocket
      this.eventEmitter.emit('order.notification', notification);

      // Log
      this.logger.debug('Notification envoyée', 'Notifications', notification);

    } catch (error) {
      this.logger.error('Erreur envoi notification', 'Notifications', {
        error,
        notification,
      });
      throw error;
    }
  }

  async sendCartUpdateNotification(cartData: any) {
    this.eventEmitter.emit('cart.updated', cartData);
  }

  async sendMessageNotification(message: any) {
    // Envoi notification temps réel
    this.eventEmitter.emit('message.new', message);

    // Email si utilisateur non connecté
    if (!message.isUserOnline) {
      await this.mailer.sendMessageEmail(message);
    }
  }
}
