import { json, LoaderFunction, ActionFunction, redirect } from "@remix-run/node";
import { useLoaderData, Form, useNavigation, Link, useActionData } from "@remix-run/react";
import { useState } from "react";
import { 
  getCart, 
  addToCart, 
  removeFromCart, 
  updateCartItemQuantity, 
  completePurchase, 
  type Cart 
} from "~/utils/cart.server";

export const loader: LoaderFunction = async ({ request }) => {
  const cart = await getCart(request);
  return json(cart);
};

export const action: ActionFunction = async ({ request }) => {
  const formData = await request.formData();
  const actionType = formData.get("action") as string;
  
  switch (actionType) {
    case "add":
      const itemToAdd = {
        id: formData.get("id") as string,
        name: formData.get("name") as string,
        quantity: parseInt(formData.get("quantity") as string) || 1,
        price: parseFloat(formData.get("price") as string) || 0,
        consigne: parseFloat(formData.get("consigne") as string) || 0,
      };
      return addToCart(request, itemToAdd);
      
    case "update":
      return updateCartItemQuantity(
        request,
        formData.get("id") as string,
        parseInt(formData.get("quantity") as string) || 1
      );
      
    case "remove":
      return removeFromCart(request, formData.get("id") as string);
      
    case "checkout":
      // Validation de base
      const cart = await getCart(request);
      if (cart.items.length === 0) {
        return json({ error: "Le panier est vide" }, { status: 400 });
      }
      return completePurchase(request);
      
    default:
      return json({ error: "Action inconnue" }, { status: 400 });
  }
};

export default function CartPage() {
  const cart = useLoaderData<typeof loader>() as Cart;
  const actionData = useActionData<{ error?: string }>();
  const navigation = useNavigation();
  const isProcessing = navigation.state !== "idle";

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold mb-6">🛒 Mon Panier</h1>
      
      {actionData?.error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {actionData.error}
        </div>
      )}

      {cart.items.length === 0 ? (
        <div className="text-center py-8">
          <p className="text-gray-500 mb-4">Votre panier est vide.</p>
          <Link to="/products" className="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
            Continuer mes achats
          </Link>
        </div>
      ) : (
        <div>
          <div className="overflow-x-auto">
            <table className="w-full border-collapse">
              <thead>
                <tr className="bg-gray-100">
                  <th className="p-2 text-left">Produit</th>
                  <th className="p-2 text-left">Prix</th>
                  <th className="p-2 text-left">Consigne</th>
                  <th className="p-2 text-left">Quantité</th>
                  <th className="p-2 text-left">Total</th>
                  <th className="p-2 text-left">Action</th>
                </tr>
              </thead>
              <tbody>
                {cart.items.map((item) => (
                  <tr key={item.id} className="border-b">
                    <td className="p-2">{item.name || item.id}</td>
                    <td className="p-2">{item.price.toFixed(2)} €</td>
                    <td className="p-2">{item.consigne.toFixed(2)} €</td>
                    <td className="p-2">
                      <Form method="post" className="flex items-center">
                        <input type="hidden" name="id" value={item.id} />
                        <input
                          type="number"
                          name="quantity"
                          min="1"
                          max="99"
                          defaultValue={item.quantity}
                          className="w-16 border rounded px-2 py-1"
                        />
                        <button
                          type="submit"
                          name="action"
                          value="update"
                          className="ml-2 bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded"
                          disabled={isProcessing}
                        >
                          ✓
                        </button>
                      </Form>
                    </td>
                    <td className="p-2">{(item.price * item.quantity).toFixed(2)} €</td>
                    <td className="p-2">
                      <Form method="post">
                        <input type="hidden" name="id" value={item.id} />
                        <button
                          type="submit"
                          name="action" 
                          value="remove"
                          className="text-red-500 hover:text-red-700"
                          disabled={isProcessing}
                        >
                          ❌
                        </button>
                      </Form>
                    </td>
                  </tr>
                ))}
              </tbody>
              <tfoot>
                <tr className="font-bold">
                  <td colSpan={4} className="p-2 text-right">Total produits:</td>
                  <td className="p-2">{cart.total.toFixed(2)} €</td>
                  <td></td>
                </tr>
                <tr className="font-bold">
                  <td colSpan={4} className="p-2 text-right">Total consigne:</td>
                  <td className="p-2">{cart.totalConsigne.toFixed(2)} €</td>
                  <td></td>
                </tr>
                <tr className="font-bold">
                  <td colSpan={4} className="p-2 text-right">Total:</td>
                  <td className="p-2">{(cart.total + cart.totalConsigne).toFixed(2)} €</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div className="mt-6 flex justify-between">
            <Link to="/products" className="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">
              Continuer mes achats
            </Link>
            <Form method="post">
              <button
                type="submit"
                name="action"
                value="checkout"
                className="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded"
                disabled={isProcessing}
              >
                {isProcessing ? "Traitement..." : "✅ Passer commande"}
              </button>
            </Form>
          </div>
        </div>
      )}
    </div>
  );
}
