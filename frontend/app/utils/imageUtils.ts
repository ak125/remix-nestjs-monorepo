import sharp from "sharp";

interface ThumbnailOptions {
  width: number;
  height: number;
  quality?: number;
  format?: 'jpeg' | 'webp';
}

export async function generateThumbnail(
  imageUrl: string, 
  options: ThumbnailOptions
): Promise<Buffer> {
  try {
    const response = await fetch(imageUrl);
    
    if (!response.ok) {
      throw new Error(`Failed to fetch image: ${response.statusText}`);
    }

    const buffer = await response.arrayBuffer();

    let pipeline = sharp(Buffer.from(buffer))
      .resize(options.width, options.height, {
        fit: 'cover',
        position: 'center',
      });

    if (options.format === 'webp') {
      return pipeline
        .webp({ quality: options.quality || 80 })
        .toBuffer();
    }

    return pipeline
      .jpeg({ quality: options.quality || 80 })
      .toBuffer();
  } catch (error) {
    console.error('Thumbnail generation error:', error);
    throw error;
  }
}

export function parseImagePath(imageName: string): string {
  const domain = "https://www.automecanik.com/upload/constructeurs-automobiles";
  return `${domain}/${imageName.replace(".ak.", "/").replace(".webp", ".jpg")}`;
}
