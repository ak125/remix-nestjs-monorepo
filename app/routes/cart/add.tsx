import { json, LoaderFunction } from "@remix-run/node";
import { z } from "zod";

// Validation des paramètres
const QuerySchema = z.object({
  recipientId: z.string().min(1, "ID article requis"),
  quantity: z.string().transform(Number).default("1"),
});

export const loader: LoaderFunction = async ({ request }) => {
  try {
    const url = new URL(request.url);
    const result = QuerySchema.safeParse(Object.fromEntries(url.searchParams));

    if (!result.success) {
      return json({ 
        error: "Paramètres invalides",
        details: result.error.format() 
      }, { 
        status: 400 
      });
    }

    const { recipientId, quantity } = result.data;

    // Appel API backend
    const response = await fetch(`${process.env.API_URL}/cart/add`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        // Transmettre les cookies de session
        Cookie: request.headers.get('Cookie') || '',
      },
      body: JSON.stringify({ 
        id: recipientId,
        quantity,
      }),
      credentials: 'include',
    });

    if (!response.ok) {
      throw new Error('Erreur ajout au panier');
    }

    const cart = await response.json();

    return json({
      success: true,
      message: `Article ${recipientId} ajouté au panier`,
      cart,
    }, {
      headers: {
        'Cache-Control': 'no-cache',
      }
    });

  } catch (error) {
    console.error('Erreur ajout au panier:', error);
    
    return json({
      error: "Impossible d'ajouter l'article au panier",
      details: error instanceof Error ? error.message : undefined
    }, {
      status: 500
    });
  }
};
