import { Injectable, Logger, BadRequestException } from '@nestjs/common';
import * as sharp from 'sharp';

@Injectable()
export class ImageService {
  private readonly logger = new Logger(ImageService.name);
  private readonly IMAGE_DOMAIN = process.env.IMAGE_DOMAIN || 'https://4714711e034622730849-4d36b3a7f81d82c66bad0a46c55c8159.ssl.cf3.rackcdn.com/products.art/';

  async generateThumbnail(params: {
    imagePath: string;
    width: number;
    height: number;
  }): Promise<Buffer> {
    const { imagePath, width, height } = params;

    try {
      // Reconstruction du chemin de l'image
      const cleanPath = this.cleanImagePath(imagePath);
      const imageUrl = `${this.IMAGE_DOMAIN}${cleanPath}`;
      
      // Téléchargement via fetch
      const response = await fetch(imageUrl);
      if (!response.ok) {
        throw new BadRequestException(`Failed to fetch image: ${imageUrl}`);
      }

      const buffer = await response.arrayBuffer();
      const image = sharp(Buffer.from(buffer));

      // Récupération des métadonnées pour le ratio
      const metadata = await image.metadata();
      const ratio = metadata.width / metadata.height;
      
      // Calcul des dimensions optimales
      const dimensions = this.calculateDimensions(width, height, ratio);

      // Génération du thumbnail
      return await image
        .resize(dimensions.width, dimensions.height, {
          fit: 'cover',
          position: 'center'
        })
        .jpeg({
          quality: 85,
          progressive: true
        })
        .toBuffer();

    } catch (error) {
      this.logger.error(`Thumbnail generation failed: ${error.message}`);
      throw new BadRequestException('Failed to generate thumbnail');
    }
  }

  private cleanImagePath(imagePath: string): string {
    return imagePath
      .replace('.ak.', '/')
      .replace(/^\/+/, '');
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
