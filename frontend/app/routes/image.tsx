import { LoaderFunction } from "@remix-run/node";
import { generateThumbnail, parseImagePath } from "~/utils/imageUtils";

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const imageName = url.searchParams.get("i");
  const width = parseInt(url.searchParams.get("w") || "200", 10);
  const height = parseInt(url.searchParams.get("h") || "200", 10);
  const format = url.searchParams.get("fmt") as 'jpeg' | 'webp' || 'jpeg';
  
  if (!imageName) {
    throw new Response("Image parameter missing", { status: 400 });
  }

  try {
    const imageUrl = parseImagePath(imageName);
    const buffer = await generateThumbnail(imageUrl, {
      width,
      height,
      format,
      quality: 80
    });

    return new Response(buffer, {
      headers: {
        "Content-Type": `image/${format}`,
        "Cache-Control": "public, max-age=86400",
        "ETag": `"${Buffer.from(imageUrl).toString('base64')}"`,
      },
    });
  } catch (error) {
    console.error("Image processing error:", error);
    throw new Response("Image processing failed", { status: 500 });
  }
};
