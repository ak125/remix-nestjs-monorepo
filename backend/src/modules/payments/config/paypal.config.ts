import { Injectable, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import * as paypal from '@paypal/checkout-server-sdk';

@Injectable()
export class PayPalConfigService {
  private readonly logger = new Logger(PayPalConfigService.name);

  constructor(private readonly configService: ConfigService) {}

  getClient() {
    const clientId = this.configService.get<string>('PAYPAL_CLIENT_ID');
    const clientSecret = this.configService.get<string>('PAYPAL_CLIENT_SECRET');
    const mode = this.configService.get<string>('PAYPAL_MODE');

    if (!clientId || !clientSecret) {
      this.logger.error('Configuration PayPal manquante');
      throw new Error('Configuration PayPal invalide');
    }

    const environment = mode === 'live'
      ? new paypal.core.LiveEnvironment(clientId, clientSecret)
      : new paypal.core.SandboxEnvironment(clientId, clientSecret);

    return new paypal.core.PayPalHttpClient(environment);
  }
}
