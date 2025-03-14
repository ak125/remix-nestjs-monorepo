import { Module, CacheModule } from '@nestjs/common';
import { ImageController } from './image.controller';
import { ImageService } from './image.service';

@Module({
  imports: [
    CacheModule.register({
      ttl: 60 * 60 * 24, // 24 heures
      max: 100 // Maximum 100 images en cache
    })
  ],
  controllers: [ImageController],
  providers: [ImageService],
  exports: [ImageService]
})
export class ImageModule {}
