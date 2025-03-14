import { json, redirect } from "@remix-run/node";
import { getSession, commitSession, destroySession } from "~/sessions";
import { getRedisClient } from "~/utils/redis.server";

export type CartItem = {
  id: string;
  name?: string;
  quantity: number;
  price: number;
  consigne: number;
  urltakentoadd?: string;
};

export type Cart = {
  items: CartItem[];
  total: number;
  totalConsigne: number;
}

/**
 * 🛒 Récupère le panier depuis la session
 */
export async function getCart(request: Request): Promise<Cart> {
  const session = await getSession(request.headers.get("Cookie"));
  const items: CartItem[] = session.get("cart") || [];
  
  // Calcul des totaux
  const total = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  const totalConsigne = items.reduce((sum, item) => sum + (item.consigne * item.quantity), 0);
  
  return { items, total, totalConsigne };
}

/**
 * 🛒 Ajoute un article au panier
 */
export async function addToCart(request: Request, item: CartItem) {
  const session = await getSession(request.headers.get("Cookie"));
  const redis = await getRedisClient();

  let cart: CartItem[] = session.get("cart") || [];

  const existingItem = cart.find((cartItem) => cartItem.id === item.id);
  if (existingItem) {
    existingItem.quantity += item.quantity;
  } else {
    cart.push(item);
  }

  // Stockage en session
  session.set("cart", cart);
  
  // Stockage de sauvegarde dans Redis avec TTL (1 jour)
  const sessionId = session.get("__session_id") || crypto.randomUUID();
  session.set("__session_id", sessionId);
  await redis.set(`cart:${sessionId}`, JSON.stringify(cart), { EX: 86400 });

  return json(
    { success: true, cart }, 
    { headers: { "Set-Cookie": await commitSession(session) } }
  );
}

/**
 * 🔄 Met à jour la quantité d'un article dans le panier
 */
export async function updateCartItemQuantity(request: Request, itemId: string, quantity: number) {
  const session = await getSession(request.headers.get("Cookie"));
  const redis = await getRedisClient();

  let cart: CartItem[] = session.get("cart") || [];
  const itemIndex = cart.findIndex((item) => item.id === itemId);
  
  if (itemIndex === -1) {
    return json({ error: "Article non trouvé dans le panier" }, { status: 404 });
  }

  if (quantity <= 0) {
    // Supprimer l'article si la quantité est 0 ou négative
    cart = cart.filter((item) => item.id !== itemId);
  } else {
    cart[itemIndex].quantity = quantity;
  }

  session.set("cart", cart);
  
  // Mise à jour dans Redis
  const sessionId = session.get("__session_id");
  if (sessionId) {
    await redis.set(`cart:${sessionId}`, JSON.stringify(cart), { EX: 86400 });
  }

  return json(
    { success: true, cart }, 
    { headers: { "Set-Cookie": await commitSession(session) } }
  );
}

/**
 * ❌ Supprime un article du panier
 */
export async function removeFromCart(request: Request, itemId: string) {
  const session = await getSession(request.headers.get("Cookie"));
  const redis = await getRedisClient();

  let cart: CartItem[] = session.get("cart") || [];
  cart = cart.filter((item) => item.id !== itemId);

  session.set("cart", cart);
  
  // Mise à jour dans Redis
  const sessionId = session.get("__session_id");
  if (sessionId) {
    await redis.set(`cart:${sessionId}`, JSON.stringify(cart), { EX: 86400 });
  }

  return json(
    { success: true, cart }, 
    { headers: { "Set-Cookie": await commitSession(session) } }
  );
}

/**
 * 🔍 Vérifie si un article est déjà dans le panier
 */
export async function isInCart(request: Request, itemId: string) {
  const { items } = await getCart(request);
  return items.some((item) => item.id === itemId);
}

/**
 * 🧹 Vide le panier
 */
export async function clearCart(request: Request) {
  const session = await getSession(request.headers.get("Cookie"));
  const redis = await getRedisClient();
  
  session.unset("cart");
  
  // Suppression dans Redis
  const sessionId = session.get("__session_id");
  if (sessionId) {
    await redis.del(`cart:${sessionId}`);
  }

  return json(
    { success: true }, 
    { headers: { "Set-Cookie": await commitSession(session) } }
  );
}

/**
 * ✅ Valide le paiement et vide le panier
 */
export async function completePurchase(request: Request) {
  const session = await getSession(request.headers.get("Cookie"));
  const redis = await getRedisClient();
  
  // Récupération du panier pour historique avant de le vider
  const cart = session.get("cart") || [];
  
  // Enregistrement dans Redis à des fins d'historique
  const purchaseId = crypto.randomUUID();
  const sessionId = session.get("__session_id");
  
  if (sessionId) {
    await redis.del(`cart:${sessionId}`);
    // Sauvegarde de l'historique d'achat (conservation 30 jours)
    await redis.set(`purchase:${purchaseId}`, JSON.stringify({
      cart,
      sessionId,
      timestamp: Date.now()
    }), { EX: 2592000 });
  }
  
  session.unset("cart");

  return redirect("/thank-you", { 
    headers: { "Set-Cookie": await commitSession(session) } 
  });
}
