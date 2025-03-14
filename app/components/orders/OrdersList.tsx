import { Link } from "@remix-run/react";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { formatDate, formatPrice } from "@/lib/format";
import { OrderStatus } from "@/lib/constants";

interface Order {
  id: number;
  status: string;
  createdAt: string;
  totalTTC: number;
  orderLines: Array<{
    piece: {
      name: string;
    };
    quantity: number;
  }>;
}

export default function OrdersList({ orders }: { orders: Order[] }) {
  return (
    <div className="space-y-4">
      {orders.map((order) => (
        <Card key={order.id} className="p-4">
          <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
              <div className="flex items-center gap-2 mb-2">
                <h3 className="font-medium">
                  Commande #{order.id}
                </h3>
                <Badge variant={OrderStatus[order.status].color}>
                  {OrderStatus[order.status].label}
                </Badge>
              </div>
              
              <p className="text-sm text-gray-500">
                {formatDate(order.createdAt)}
              </p>
              
              <p className="text-sm text-gray-600 mt-1">
                {order.orderLines.length} article(s)
              </p>
            </div>

            <div className="flex items-center gap-4">
              <p className="font-medium">
                {formatPrice(order.totalTTC)}
              </p>
              
              <Link to={`/orders/${order.id}`}>
                <Button variant="outline" size="sm">
                  Voir détails
                </Button>
              </Link>
            </div>
          </div>
        </Card>
      ))}
    </div>
  );
}
