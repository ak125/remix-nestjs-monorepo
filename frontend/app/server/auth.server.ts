import { getSession } from "~/server/session.server";

/**
 * ✅ Récupérer l'utilisateur connecté depuis la session
 */
export async function getUserSession(request: Request) {
  const session = await getSession(request);
  return session.get("user") || null;
}
