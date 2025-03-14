import { getUserSession } from "~/server/auth.server";
import { prisma } from "~/server/db.server";

/**
 * ✅ Récupérer les informations d'un utilisateur connecté
 */
export async function getAuthenticatedUser(request: Request) {
  const sessionUser = await getUserSession(request);
  if (!sessionUser) return null;

  const user = await prisma.user.findUnique({
    where: { id: sessionUser.id },
    select: {
      id: true,
      email: true,
      name: true,
      address: true,
      zipcode: true,
      city: true,
      country: true,
    },
  });

  return user;
}
