import { Controller, Post, UseInterceptors, UploadedFile, Session } from '@nestjs/common';
import { FileInterceptor } from '@nestjs/platform-express';
import { ImageProcessorService } from './image-processor.service';
import { diskStorage } from 'multer';
import { extname } from 'path';

@Controller('upload')
export class UploadController {
  constructor(private imageProcessor: ImageProcessorService) {}

  @Post('image')
  @UseInterceptors(FileInterceptor('file', {
    storage: diskStorage({
      destination: './uploads/temp',
      filename: (_, file, cb) => {
        const randomName = Array(32)
          .fill(null)
          .map(() => Math.round(Math.random() * 16).toString(16))
          .join('');
        return cb(null, `${randomName}${extname(file.originalname)}`);
      }
    }),
    fileFilter: (_, file, cb) => {
      if (!file.mimetype.match(/^image\/(jpg|jpeg|png|gif)$/)) {
        return cb(new Error('Only image files are allowed!'), false);
      }
      cb(null, true);
    }
  }))
  async uploadImage(
    @UploadedFile() file: Express.Multer.File,
    @Session() session: Record<string, any>
  ) {
    return this.imageProcessor.processImage(file, session.userId);
  }
}
