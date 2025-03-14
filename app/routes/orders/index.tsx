import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { getSession } from "@/utils/session.server";
import { formatPrice, formatDate } from "@/lib/format";
import { OrderStatus } from "@/lib/constants";

interface OrderLine {
  id: number;
  quantity: number;
  priceHT: number;
  priceTTC: number;
  consigneHT?: number;
  consigneTTC?: number;
  piece: {
    name: string;
    reference: string;
  };
}

interface Order {
  id: number;
  status: string;
  createdAt: string;
  totalAmount: number;
  totalHT: number;
  isPaid: boolean;
  orderLines: OrderLine[];
}

export const loader: LoaderFunction = async ({ request }) => {
  const session = await getSession(request.headers.get("Cookie"));
  
  if (!session.has("userId")) {
    return json({ 
      error: "Session expirée" 
    }, { 
      status: 401 
    });
  }

  const orders = await fetch(`${process.env.API_URL}/orders`, {
    headers: { Cookie: request.headers.get("Cookie") || "" },
  }).then(r => r.json());

  return json({ orders });
};

function OrderStatus({ order }: { order: Order }) {
  if (order.status === 'REFUNDED') {
    return (
      <div className="text-orange-600">
        <Badge variant="warning">Remboursé</Badge>
        <p className="text-sm mt-1">
          {formatPrice(order.refundedAmount)} remboursés
        </p>
      </div>
    );
  }

  if (order.status === 'PARTIALLY_REFUNDED') {
    return (
      <div className="text-blue-600">
        <Badge variant="info">Remboursement partiel</Badge>
        <p className="text-sm mt-1">
          {formatPrice(order.refundedAmount)} / {formatPrice(order.totalAmount)}
        </p>
      </div>
    );
  }

  return (
    <Badge variant={getStatusColor(order.status)}>
      {getStatusLabel(order.status)}
    </Badge>
  );
}

export default function OrdersPage() {
  const { orders } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-6">
      <h1 className="text-2xl font-bold mb-6">Mes commandes</h1>

      <div className="space-y-4">
        {orders.map((order) => (
          <Card key={order.id}>
            <div className="p-4">
              <div className="flex justify-between items-start">
                <div>
                  <h2 className="font-medium">
                    Commande #{order.id}
                  </h2>
                  <p className="text-sm text-gray-500">
                    {formatDate(order.createdAt)}
                  </p>
                </div>

                <div className="flex items-center gap-2">
                  <OrderStatus order={order} />

                  {order.status === 'PAID' && (
                    <RefundButton
                      orderId={order.id}
                      orderAmount={order.totalAmount}
                      onRefunded={() => handleRefunded(order.id)}
                    />
                  )}
                </div>
              </div>

              {/* ...reste du contenu... */}

              {(order.status === 'REFUNDED' || order.status === 'PARTIALLY_REFUNDED') && (
                <RefundsHistory
                  refunds={order.refunds}
                  totalAmount={order.totalAmount}
                  refundedAmount={order.refundedAmount}
                />
              )}
            </div>
          </Card>
        ))}
      </div>
    </div>
  );
}
