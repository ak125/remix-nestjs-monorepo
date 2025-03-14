import { Module } from '@nestjs/common';
import { GammeController } from './controllers/gamme.controller';
import { GammeService } from './services/gamme.service';

@Module({
  controllers: [GammeController],
  providers: [GammeService],
  exports: [GammeService]
})
export class GammeModule {}
