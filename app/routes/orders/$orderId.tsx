import { json, LoaderFunction, MetaFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { formatDate, formatPrice } from "@/lib/format";
import { getSession } from "@/utils/session.server";
import { OrderStatus } from "@/lib/constants";

export const meta: MetaFunction = ({ data }) => ({
  title: `Commande #${data?.order?.id} - Détails`,
  description: `Détails de votre commande ${data?.order?.id}`,
});

export const loader: LoaderFunction = async ({ request, params }) => {
  const session = await getSession(request.headers.get("Cookie"));
  
  if (!session.has("userId")) {
    throw new Response("Non authentifié", { status: 401 });
  }

  const response = await fetch(`${process.env.API_URL}/orders/${params.orderId}`, {
    headers: { Cookie: request.headers.get("Cookie") || "" },
  });

  if (!response.ok) {
    throw new Response("Commande introuvable", { status: 404 });
  }

  const order = await response.json();
  return json({ order });
};

export default function OrderDetailPage() {
  const { order } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-6">
      <Card className="max-w-4xl mx-auto">
        <div className="p-6">
          <div className="flex justify-between items-start mb-6">
            <div>
              <h1 className="text-2xl font-bold">
                Commande #{order.id}
              </h1>
              <p className="text-sm text-gray-500">
                {formatDate(order.createdAt)}
              </p>
            </div>
            
            <Badge variant={OrderStatus[order.status].color}>
              {OrderStatus[order.status].label}
            </Badge>
          </div>

          {/* Détails commande */}
          <div className="space-y-6">
            {/* Articles */}
            <div>
              <h2 className="font-medium mb-2">Articles</h2>
              <div className="space-y-2">
                {order.orderLines.map((line) => (
                  <div 
                    key={line.id}
                    className="flex justify-between py-2 border-b"
                  >
                    <div>
                      <p className="font-medium">{line.piece.name}</p>
                      <p className="text-sm text-gray-500">
                        Réf: {line.piece.reference}
                      </p>
                    </div>
                    <div className="text-right">
                      <p>{formatPrice(line.priceTTC)}</p>
                      <p className="text-sm text-gray-500">
                        Quantité: {line.quantity}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Totaux */}
            <div className="pt-4 border-t">
              <div className="space-y-2">
                <div className="flex justify-between">
                  <span>Total HT</span>
                  <span>{formatPrice(order.totalHT)}</span>
                </div>
                <div className="flex justify-between">
                  <span>TVA</span>
                  <span>{formatPrice(order.totalTTC - order.totalHT)}</span>
                </div>
                {order.totalConsigne > 0 && (
                  <div className="flex justify-between">
                    <span>Consigne</span>
                    <span>{formatPrice(order.totalConsigne)}</span>
                  </div>
                )}
                <div className="flex justify-between font-bold pt-2 border-t">
                  <span>Total TTC</span>
                  <span>{formatPrice(order.totalTTC)}</span>
                </div>
              </div>
            </div>

            {/* Adresses */}
            <div className="grid md:grid-cols-2 gap-6 pt-6">
              <div>
                <h2 className="font-medium mb-2">Adresse de facturation</h2>
                <address className="not-italic">
                  {order.billingAddress.civility} {order.billingAddress.firstName} {order.billingAddress.lastName}<br />
                  {order.billingAddress.address}<br />
                  {order.billingAddress.zipCode} {order.billingAddress.city}<br />
                  {order.billingAddress.country}
                </address>
              </div>
              
              <div>
                <h2 className="font-medium mb-2">Adresse de livraison</h2>
                <address className="not-italic">
                  {order.shippingAddress.civility} {order.shippingAddress.firstName} {order.shippingAddress.lastName}<br />
                  {order.shippingAddress.address}<br />
                  {order.shippingAddress.zipCode} {order.shippingAddress.city}<br />
                  {order.shippingAddress.country}
                </address>
              </div>
            </div>
          </div>
        </div>
      </Card>
    </div>
  );
}
