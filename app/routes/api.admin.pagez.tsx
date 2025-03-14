import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import { requireAdmin } from "~/lib/admin-session.server";

export async function loader({ request }) {
  // Vérifier que l'utilisateur est administrateur avec niveau >= 7
  await requireAdmin(request, 7);
  
  try {
    const pages = await prisma.pageZ.findMany({
      orderBy: [
        { mfId: 'asc' },
        { pgName: 'asc' }
      ]
    });
    
    return json(pages);
  } catch (error) {
    console.error("Error fetching PageZ data:", error);
    return json({ error: "Une erreur est survenue lors de la récupération des données" }, { status: 500 });
  }
}

export async function action({ request }) {
  // Vérifier que l'utilisateur est administrateur avec niveau >= 7
  const admin = await requireAdmin(request, 7);
  
  // Traiter uniquement les requêtes POST pour les mises à jour
  if (request.method !== "POST") {
    return json({ error: "Méthode non autorisée" }, { status: 405 });
  }
  
  try {
    const formData = await request.formData();
    const id = formData.get("id") ? parseInt(formData.get("id") as string) : null;
    const mfId = parseInt(formData.get("mfId") as string);
    const pgId = parseInt(formData.get("pgId") as string);
    const pgName = formData.get("pgName") as string;
    const mfName = formData.get("mfName") as string;
    const seoTitle = formData.get("seoTitle") as string;
    const seoDesc = formData.get("seoDesc") as string;
    const content = formData.get("content") as string;
    
    // Valider les données
    if (!mfId || !pgId || !pgName || !mfName) {
      return json({ error: "Données invalides" }, { status: 400 });
    }
    
    // Mise à jour ou création
    const page = await prisma.pageZ.upsert({
      where: { 
        id: id || -1 // Si id est null, utilisez une valeur qui ne sera pas trouvée
      },
      update: {
        pgName,
        mfName,
        seoTitle,
        seoDesc,
        content
      },
      create: {
        mfId,
        pgId,
        pgName,
        mfName,
        seoTitle,
        seoDesc,
        content
      }
    });
    
    return json({ 
      success: true, 
      message: "Données mises à jour avec succès", 
      page 
    });
  } catch (error) {
    console.error("Error updating PageZ data:", error);
    return json({ 
      error: "Une erreur est survenue lors de la mise à jour des données"
    }, { status: 500 });
  }
}
