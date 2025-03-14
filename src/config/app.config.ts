import { Injectable } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';

@Injectable()
export class AppConfigService {
  constructor(private config: ConfigService) {}

  get isDev(): boolean {
    return this.config.get('NODE_ENV') === 'development';
  }

  get domain(): string {
    return this.config.get('DOMAIN') || 'https://www.automecanik.com/massdoc';
  }

  get domainParent(): string {
    return this.config.get('DOMAIN_PARENT') || 'https://www.automecanik.com';
  }

  get currency(): string {
    return this.config.get('CURRENCY') || '€';
  }

  get databaseUrl(): string {
    return this.config.get('DATABASE_URL')!;
  }

  get supabase() {
    return {
      url: this.config.get('SUPABASE_URL')!,
      key: this.config.get('SUPABASE_ANON_KEY')!
    };
  }

  get stripe() {
    return {
      secretKey: this.config.get('STRIPE_SECRET_KEY')!,
      webhookSecret: this.config.get('STRIPE_WEBHOOK_SECRET')!
    };
  }
}
