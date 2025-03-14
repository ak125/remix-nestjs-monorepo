import { Module, Global } from '@nestjs/common';
import { PrismaService } from './prisma.service';
import { ConfigModule, ConfigService } from '@nestjs/config';

@Global()
@Module({
  imports: [ConfigModule],
  providers: [
    {
      provide: PrismaService,
      useFactory: (config: ConfigService) => {
        const prisma = new PrismaService();
        
        // Configuration des logs en dev
        if (config.get('NODE_ENV') === 'development') {
          prisma.$on('query', (event) => {
            console.log('Query:', event.query);
            console.log('Duration:', event.duration + 'ms');
          });
        }
        
        return prisma;
      },
      inject: [ConfigService],
    },
  ],
  exports: [PrismaService],
})
export class PrismaModule {}
