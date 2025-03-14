import { Injectable, Logger, LogLevel } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';

@Injectable()
export class LoggerService {
  private readonly logger: Logger;
  private readonly logLevels: LogLevel[];

  constructor(
    context: string,
    private readonly configService: ConfigService,
  ) {
    this.logger = new Logger(context);
    this.logLevels = this.getLogLevels();
  }

  log(message: string, context?: string, data?: any) {
    if (this.isLevelEnabled('log')) {
      this.logger.log(
        this.formatMessage(message, data),
        context || 'Application',
      );
    }
  }

  error(message: string, context?: string, error?: any, stack?: string) {
    if (this.isLevelEnabled('error')) {
      this.logger.error(
        this.formatMessage(message, error),
        stack || error?.stack || '',
        context || 'Error',
      );
    }
  }

  warn(message: string, context?: string, data?: any) {
    if (this.isLevelEnabled('warn')) {
      this.logger.warn(this.formatMessage(message, data), context || 'Warning');
    }
  }

  debug(message: string, context?: string, data?: any) {
    if (this.isLevelEnabled('debug')) {
      this.logger.debug(this.formatMessage(message, data), context || 'Debug');
    }
  }

  verbose(message: string, context?: string, data?: any) {
    if (this.isLevelEnabled('verbose')) {
      this.logger.verbose(
        this.formatMessage(message, data),
        context || 'Verbose',
      );
    }
  }

  private formatMessage(message: string, data?: any): string {
    if (!data) return message;

    const formattedData =
      typeof data === 'object'
        ? JSON.stringify(data, null, 2)
        : data.toString();

    return `${message} - ${formattedData}`;
  }

  private getLogLevels(): LogLevel[] {
    const env = this.configService.get('NODE_ENV', 'development');

    if (env === 'production') {
      return ['log', 'warn', 'error'];
    }

    return ['log', 'error', 'warn', 'debug', 'verbose'];
  }

  private isLevelEnabled(level: LogLevel): boolean {
    return this.logLevels.includes(level);
  }
}
