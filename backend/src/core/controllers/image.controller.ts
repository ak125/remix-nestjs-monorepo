import { 
  Controller, 
  Get, 
  Query, 
  Res,
  ParseIntPipe,
  DefaultValuePipe,
  CacheInterceptor,
  UseInterceptors,
  ValidationPipe
} from '@nestjs/common';
import { Response } from 'express';
import { ImageService } from '../services/image.service';

@Controller('image')
@UseInterceptors(CacheInterceptor)
export class ImageController {
  constructor(private readonly imageService: ImageService) {}

  @Get('thumbnail')
  async getThumbnail(
    @Query('i') imagePath: string,
    @Query('w', new ParseIntPipe(), new DefaultValuePipe(200)) width: number,
    @Query('h', new ParseIntPipe(), new DefaultValuePipe(200)) height: number,
    @Res() res: Response
  ) {
    const buffer = await this.imageService.generateThumbnail({
      imagePath,
      width,
      height
    });

    res.set({
      'Content-Type': 'image/jpeg',
      'Cache-Control': 'public, max-age=31536000',
      'ETag': `"${Buffer.from(imagePath).toString('base64')}"`,
    });

    return res.send(buffer);
  }
}
