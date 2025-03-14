import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import { requireAdmin } from "~/lib/admin-session.server";

export async function loader({ request, params }) {
  // Vérifier que l'utilisateur est administrateur avec niveau >= 7
  await requireAdmin(request, 7);
  
  const { id } = params;
  
  if (!id || isNaN(parseInt(id))) {
    return json({ error: "ID invalide" }, { status: 400 });
  }
  
  try {
    const page = await prisma.pageZ.findUnique({
      where: { id: parseInt(id) }
    });
    
    if (!page) {
      return json({ error: "Page non trouvée" }, { status: 404 });
    }
    
    return json(page);
  } catch (error) {
    console.error(`Error fetching PageZ #${id}:`, error);
    return json({ error: "Une erreur est survenue lors de la récupération des données" }, { status: 500 });
  }
}
