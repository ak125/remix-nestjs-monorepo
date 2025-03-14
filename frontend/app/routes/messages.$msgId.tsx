import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { getUserSession } from "~/server/auth.server";
import { prisma } from "~/server/db.server";

/**
 * ✅ Vérification de l'utilisateur via session et récupération du message
 */
export const loader: LoaderFunction = async ({ request, params }) => {
  const user = await getUserSession(request);
  if (!user) {
    return json({ error: "Accès refusé" }, { status: 401 });
  }

  const { msgId } = params;
  if (!msgId) {
    return json({ error: "Message introuvable" }, { status: 404 });
  }

  const message = await prisma.xTR_MSG.findUnique({
    where: {
      MSG_ID: msgId,
      MSG_CST_ID: user.id, // Assure que l'utilisateur ne peut voir que ses messages
    },
  });

  if (!message) {
    return json({ error: "Accès interdit" }, { status: 403 });
  }

  return json({ message });
};

/**
 * ✅ Page affichant un message spécifique
 */
export default function MessagePage() {
  const { message, error } = useLoaderData<typeof loader>();

  if (error) {
    return (
      <div className="container mx-auto p-6">
        <h1 className="text-2xl font-bold text-red-600">Erreur</h1>
        <p>{error}</p>
      </div>
    );
  }

  return (
    <div className="container mx-auto p-6">
      <h1 className="text-2xl font-bold">{message.MSG_SUBJECT}</h1>
      <div className="mt-4 p-4 border rounded-md bg-gray-100">
        <p dangerouslySetInnerHTML={{ __html: message.MSG_CONTENT }} />
      </div>
    </div>
  );
}
