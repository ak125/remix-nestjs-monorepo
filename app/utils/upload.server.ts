import { writeAsyncIterableToFile } from "@remix-run/node";
import { nanoid } from "nanoid";
import * as path from "path";

const UPLOAD_DIR = path.join(process.cwd(), "public/uploads");

export async function uploadHandler(file: File): Promise<string> {
  const extension = path.extname(file.name);
  const filename = `${nanoid()}${extension}`;
  const filepath = path.join(UPLOAD_DIR, filename);

  await writeAsyncIterableToFile(
    file.stream(),
    filepath
  );

  return `/uploads/${filename}`;
}
