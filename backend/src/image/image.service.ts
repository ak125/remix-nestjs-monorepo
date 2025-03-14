import { Injectable, Logger } from '@nestjs/common';
import * as sharp from 'sharp';
import * as path from 'path';

@Injectable()
export class ImageService {
  private readonly logger = new Logger(ImageService.name);
  private readonly domain = process.env.IMAGES_DOMAIN || 'https://www.automecanik.com';
  private readonly uploadsPath = process.env.UPLOADS_PATH || '/upload/articles';

  async generateThumbnail(params: {
    imagePath: string;
    width: number;
    height: number;
    format?: 'jpeg' | 'webp';
  }): Promise<Buffer> {
    const { imagePath, width, height, format = 'jpeg' } = params;

    try {
      // Reconstruction du chemin de l'image
      const cleanPath = this.cleanImagePath(imagePath);
      const imageUrl = `${this.domain}${this.uploadsPath}/${cleanPath}`;

      // Téléchargement de l'image
      const response = await fetch(imageUrl);
      if (!response.ok) {
        throw new Error(`Failed to fetch image: ${imageUrl}`);
      }

      const buffer = await response.arrayBuffer();

      // Génération de la miniature
      const image = sharp(Buffer.from(buffer));

      // Métadonnées pour le ratio
      const metadata = await image.metadata();
      const ratio = metadata.width / metadata.height;

      // Calcul des dimensions optimales
      const dimensions = this.calculateDimensions(width, height, ratio);

      return await image
        .resize(dimensions.width, dimensions.height, {
          fit: 'cover',
          position: 'center'
        })
        [format]({
          quality: 85,
          progressive: true
        })
        .toBuffer();

    } catch (error) {
      this.logger.error(`Thumbnail generation failed: ${error.message}`, error.stack);
      throw error;
    }
  }

  private cleanImagePath(imagePath: string): string {
    return imagePath
      .replace('.ak.', '/')
      .replace('.webp', '.jpg')
      .replace(/^\/+/, ''); // Remove leading slashes
  }

  private calculateDimensions(targetWidth: number, targetHeight: number, ratio: number) {
    if (targetWidth / targetHeight > ratio) {
      return {
        width: Math.round(targetHeight * ratio),
        height: targetHeight
      };
    }
    return {
      width: targetWidth,
      height: Math.round(targetWidth / ratio)
    };
  }
}
