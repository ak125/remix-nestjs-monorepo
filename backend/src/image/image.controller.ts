import { 
  Controller, 
  Get, 
  Query, 
  Res,
  ParseIntPipe,
  DefaultValuePipe,
  ValidationPipe,
  CacheInterceptor,
  UseInterceptors
} from '@nestjs/common';
import { Response } from 'express';
import { ImageService } from './image.service';

@Controller('image')
@UseInterceptors(CacheInterceptor)
export class ImageController {
  constructor(private readonly imageService: ImageService) {}

  @Get('thumbnail')
  async getThumbnail(
    @Query('i') imagePath: string,
    @Query('w', new ParseIntPipe(), new DefaultValuePipe(200)) width: number,
    @Query('h', new ParseIntPipe(), new DefaultValuePipe(200)) height: number,
    @Query('f', new DefaultValuePipe('jpeg')) format: 'jpeg' | 'webp',
    @Res() res: Response
  ) {
    try {
      const buffer = await this.imageService.generateThumbnail({
        imagePath,
        width,
        height,
        format
      });

      res.set({
        'Content-Type': `image/${format}`,
        'Cache-Control': 'public, max-age=31536000',
        'ETag': `"${Buffer.from(imagePath).toString('base64')}"`,
      });

      return res.send(buffer);

    } catch (error) {
      res.status(500).send({
        error: 'Thumbnail generation failed',
        message: error.message
      });
    }
  }
}
