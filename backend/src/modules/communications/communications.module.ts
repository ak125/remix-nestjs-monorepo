import { Module } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { EventEmitterModule } from '@nestjs/event-emitter';
import { MailerService } from './services/mailer.service';
import { NotificationsService } from './services/notifications.service';
import { MessagesGateway } from './gateways/messages.gateway';
import { NotificationsGateway } from './gateways/notifications.gateway';
import { LoggerService } from '../../common/services/logger.service';
import { SharedModule } from '../shared/shared.module';

@Module({
  imports: [
    SharedModule,
    ConfigModule,
    EventEmitterModule.forRoot({
      wildcard: false,
      delimiter: '.',
      maxListeners: 10,
    }),
  ],

  providers: [
    MailerService,
    NotificationsService,
    MessagesGateway,
    NotificationsGateway,
    LoggerService,

    // Configuration email
    {
      provide: 'MAIL_CONFIG',
      useFactory: (config: ConfigService) => ({
        host: config.get('SMTP_HOST'),
        port: config.get('SMTP_PORT'),
        secure: true,
        auth: {
          user: config.get('SMTP_USER'),
          pass: config.get('SMTP_PASS'),
        },
      }),
      inject: [ConfigService],
    },
  ],

  exports: [
    MailerService,
    NotificationsService,
  ],
})
export class CommunicationsModule {}
