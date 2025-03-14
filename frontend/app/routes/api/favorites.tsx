import { json, ActionFunction } from "@remix-run/node";
import { requireUser } from "~/utils/session.server";
import { prisma } from "~/utils/db.server";

export const action: ActionFunction = async ({ request }) => {
  const user = await requireUser(request);
  const formData = await request.formData();
  const modelId = Number(formData.get("modelId"));
  const action = formData.get("action");

  if (!modelId) {
    return json({ error: "Model ID required" }, { status: 400 });
  }

  try {
    if (action === "add") {
      await prisma.userFavorite.create({
        data: { userId: user.id, modelId }
      });
    } else if (action === "remove") {
      await prisma.userFavorite.deleteMany({
        where: { userId: user.id, modelId }
      });
    }

    const favorites = await prisma.userFavorite.findMany({
      where: { userId: user.id },
      select: { modelId: true }
    });

    return json({ favorites });
  } catch (error) {
    return json({ error: "Database error" }, { status: 500 });
  }
};

export const loader = async ({ request }) => {
  const user = await requireUser(request);
  
  const favorites = await prisma.userFavorite.findMany({
    where: { userId: user.id },
    include: { model: true }
  });

  return json({ favorites });
};
