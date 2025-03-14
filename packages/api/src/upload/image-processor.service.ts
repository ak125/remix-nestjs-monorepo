import { Injectable, Logger } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import * as sharp from 'sharp';
import { join } from 'path';
import { z } from 'zod';

const imageConfigSchema = z.object({
  uploadDir: z.string(),
  sizes: z.array(z.object({
    name: z.string(),
    width: z.number(),
    height: z.number(),
    fit: z.enum(['cover', 'contain', 'fill']).default('cover')
  }))
});

@Injectable()
export class ImageProcessorService {
  private readonly logger = new Logger(ImageProcessorService.name);
  private readonly config = imageConfigSchema.parse({
    uploadDir: join(process.cwd(), 'uploads'),
    sizes: [
      { name: 'mini', width: 120, height: 120 },
      { name: 'medium', width: 380, height: 220 },
      { name: 'large', width: 800, height: 400, fit: 'contain' }
    ]
  });

  constructor(private prisma: PrismaService) {}

  async processImage(file: Express.Multer.File, userId: string) {
    const timestamp = Date.now();
    const filename = `${timestamp}_${file.originalname.replace(/\s+/g, '-')}`;

    return this.prisma.$transaction(async (tx) => {
      // Create image record
      const image = await tx.image.create({
        data: {
          filename,
          originalName: file.originalname,
          mimeType: file.mimetype,
          size: file.size,
          uploadedBy: userId,
          variants: {
            create: await this.generateVariants(file.buffer, filename)
          }
        }
      });

      // Log action
      await tx.actionLog.create({
        data: {
          type: 'image_uploaded',
          details: {
            imageId: image.id,
            filename,
            variants: this.config.sizes.map(s => s.name)
          },
          userId
        }
      });

      return image;
    });
  }

  private async generateVariants(buffer: Buffer, filename: string) {
    const variants = [];

    for (const size of this.config.sizes) {
      const variantPath = join(this.config.uploadDir, size.name);
      const variantFilename = `${size.name}_${filename}`;
      
      try {
        await sharp(buffer)
          .resize(size.width, size.height, { fit: size.fit })
          .toFile(join(variantPath, variantFilename));

        variants.push({
          name: size.name,
          path: `${size.name}/${variantFilename}`,
          width: size.width,
          height: size.height
        });
      } catch (error) {
        this.logger.error(`Error generating ${size.name} variant: ${error.message}`);
      }
    }

    return variants;
  }
}
