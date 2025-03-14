import { json } from "@remix-run/node";
import { prisma } from "~/lib/prisma.server";
import { requireUserId } from "~/utils/auth.server";

export const action = async ({ request }: { request: Request }) => {
  const userId = await requireUserId(request);
  const formData = await request.formData();
  const modelId = Number(formData.get("modelId"));

  // Toggle favorite status
  const existingFavorite = await prisma.userFavorite.findUnique({
    where: {
      userId_modelId: {
        userId,
        modelId
      }
    }
  });

  if (existingFavorite) {
    await prisma.userFavorite.delete({
      where: { id: existingFavorite.id }
    });
  } else {
    await prisma.userFavorite.create({
      data: {
        userId,
        modelId
      }
    });
  }

  return json({ success: true });
};

export const loader = async ({ request }: { request: Request }) => {
  const userId = await requireUserId(request);
  
  const favorites = await prisma.userFavorite.findMany({
    where: { userId },
    include: {
      model: {
        include: {
          specifications: true
        }
      }
    }
  });

  return json({ favorites });
};
