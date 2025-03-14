import { Module, Global } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { RedisModule } from './redis/redis.module';
import { MailerModule } from './mailer/mailer.module';
import { WebsocketModule } from './websocket/websocket.module';

@Global()
@Module({
  imports: [
    RedisModule,
    MailerModule,
    WebsocketModule,
    ConfigModule,
  ],
  exports: [
    RedisModule,
    MailerModule,
    WebsocketModule,
  ],
})
export class SharedModule {}
