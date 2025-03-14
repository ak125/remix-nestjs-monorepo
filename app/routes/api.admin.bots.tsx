import { ActionFunction, json, redirect } from "@remix-run/node";

export const action: ActionFunction = async ({ request }) => {
  const apiBaseUrl = process.env.API_BASE_URL || "http://localhost:3000";
  const formData = await request.formData();
  const action = formData.get("_action");
  
  try {
    // Action pour ajouter un bot
    if (action === "add") {
      const name = formData.get("name") as string;
      const reason = formData.get("reason") as string || "Ajouté manuellement";
      
      if (!name) {
        return json({ success: false, error: "Le nom du bot est requis" }, { status: 400 });
      }
      
      const response = await fetch(`${apiBaseUrl}/api/bots`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ name, reason })
      });
      
      if (!response.ok) {
        throw new Error(`Erreur lors de l'ajout du bot: ${response.status}`);
      }
      
      return redirect("/admin/bots");
    }
    
    // Action pour supprimer un bot
    if (action === "delete") {
      const name = formData.get("name") as string;
      
      if (!name) {
        return json({ success: false, error: "Le nom du bot est requis" }, { status: 400 });
      }
      
      const response = await fetch(`${apiBaseUrl}/api/bots/${encodeURIComponent(name)}`, {
        method: "DELETE"
      });
      
      if (!response.ok) {
        throw new Error(`Erreur lors de la suppression du bot: ${response.status}`);
      }
      
      return redirect("/admin/bots");
    }
    
    return json({ success: false, error: "Action non supportée" }, { status: 400 });
  } catch (error) {
    console.error("Erreur lors de l'action sur les bots:", error);
    return json({ 
      success: false, 
      error: error instanceof Error ? error.message : "Une erreur est survenue" 
    }, { status: 500 });
  }
};

export default function BotsApi() {
  // Cette route est utilisée uniquement pour les actions, pas pour l'affichage
  return null;
}
