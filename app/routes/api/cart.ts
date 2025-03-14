import { json, ActionFunction, LoaderFunction } from "@remix-run/node";
import { 
  getCart, 
  addToCart, 
  removeFromCart, 
  updateCartItemQuantity, 
  clearCart 
} from "~/utils/cart.server";

export const loader: LoaderFunction = async ({ request }) => {
  // Sécurité: vérifier origin/referer pour éviter les CSRF
  const origin = request.headers.get("Origin") || "";
  const host = request.headers.get("Host") || "";
  
  if (!origin.includes(host) && process.env.NODE_ENV === "production") {
    return json({ error: "Unauthorized" }, { status: 403 });
  }
  
  // Uniquement pour récupérer les informations du panier
  const cart = await getCart(request);
  return json(cart);
};

export const action: ActionFunction = async ({ request }) => {
  try {
    // Vérification de sécurité pour les requêtes cross-origin
    const origin = request.headers.get("Origin") || "";
    const host = request.headers.get("Host") || "";
    
    if (!origin.includes(host) && process.env.NODE_ENV === "production") {
      return json({ error: "Unauthorized" }, { status: 403 });
    }

    const contentType = request.headers.get("Content-Type") || "";
    let data;
    
    if (contentType.includes("application/json")) {
      data = await request.json();
    } else {
      const formData = await request.formData();
      data = Object.fromEntries(formData);
    }

    const { action, id } = data;

    switch (action) {
      case "add":
        if (!id || !data.price || data.price < 0) {
          return json({ error: "Données invalides" }, { status: 400 });
        }
        
        return addToCart(request, {
          id,
          name: data.name || id,
          quantity: parseInt(data.quantity) || 1,
          price: parseFloat(data.price) || 0,
          consigne: parseFloat(data.consigne) || 0,
        });
        
      case "remove":
        if (!id) {
          return json({ error: "ID produit requis" }, { status: 400 });
        }
        return removeFromCart(request, id);
        
      case "update":
        if (!id || !data.quantity) {
          return json({ error: "ID produit et quantité requis" }, { status: 400 });
        }
        return updateCartItemQuantity(request, id, parseInt(data.quantity) || 1);
        
      case "clear":
        return clearCart(request);
        
      default:
        return json({ error: "Action inconnue" }, { status: 400 });
    }
  } catch (error) {
    console.error("Erreur API panier:", error);
    return json({ error: "Erreur serveur" }, { status: 500 });
  }
};
