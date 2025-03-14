import { useLoaderData, useNavigation } from '@remix-run/react';
import { json, LoaderFunctionArgs } from '@remix-run/node';
import { z } from 'zod';
import { useOrderSocket } from '~/hooks/useOrderSocket';

// Validation des données de commande
const OrderSchema = z.object({
  order_id: z.string(),
  order_date: z.string(),
  order_total: z.number(),
  order_status: z.string(),
  xTR_CUSTOMER: z.object({
    CST_NAME: z.string(),
    CST_FNAME: z.string(),
  }),
  xTR_ORDER_ITEMS: z.array(z.object({
    piece_id: z.string(),
    quantity: z.number(),
    price: z.number(),
    xTR_PIECE: z.object({
      piece_name: z.string(),
    }),
  })),
});

export async function loader({ params }: LoaderFunctionArgs) {
  try {
    const response = await fetch(`${process.env.API_URL}/orders/${params.orderId}`, {
      credentials: 'include',
    });

    if (!response.ok) {
      throw new Error('Commande non trouvée');
    }

    const data = await response.json();
    const order = OrderSchema.parse(data);

    return json({ order });
  } catch (error) {
    throw new Error('Impossible de charger la commande');
  }
}

export default function OrderDetailsPage() {
  const { order, sessionId } = useLoaderData<typeof loader>();
  const navigation = useNavigation();
  
  // Connexion WebSocket
  const { isConnected, lastUpdate } = useOrderSocket(sessionId);

  if (navigation.state === "loading") {
    return <div className="loading">Chargement...</div>;
  }

  return (
    <div className="order-details">
      <div className="connection-status">
        {isConnected ? '🟢 Connecté' : '🔴 Déconnecté'}
      </div>
      <h1>Commande #{order.order_id}</h1>
      
      <div className="order-header">
        <p className="customer-name">
          <strong>Client:</strong> {order.xTR_CUSTOMER.CST_FNAME} {order.xTR_CUSTOMER.CST_NAME}
        </p>
        <p className="order-date">
          <strong>Date:</strong> {new Date(order.order_date).toLocaleDateString('fr-FR')}
        </p>
        <p className="order-status">
          <strong>Statut:</strong> {order.order_status}
        </p>
      </div>

      <div className="order-items">
        <h2>Articles commandés</h2>
        <table>
          <thead>
            <tr>
              <th>Article</th>
              <th>Quantité</th>
              <th>Prix unitaire</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            {order.xTR_ORDER_ITEMS.map(item => (
              <tr key={item.piece_id}>
                <td>{item.xTR_PIECE.piece_name}</td>
                <td>{item.quantity}</td>
                <td>{item.price.toFixed(2)}€</td>
                <td>{(item.quantity * item.price).toFixed(2)}€</td>
              </tr>
            ))}
          </tbody>
          <tfoot>
            <tr>
              <td colSpan={3}><strong>Total</strong></td>
              <td><strong>{order.order_total.toFixed(2)}€</strong></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <style jsx>{`
        .order-details {
          padding: 2rem;
          max-width: 1200px;
          margin: 0 auto;
        }

        .order-header {
          background: #f8f9fa;
          padding: 1rem;
          border-radius: 8px;
          margin: 1rem 0;
        }

        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 1rem;
        }

        th, td {
          padding: 0.75rem;
          border-bottom: 1px solid #dee2e6;
          text-align: left;
        }

        th {
          background: #f8f9fa;
          font-weight: 600;
        }

        tfoot td {
          background: #f8f9fa;
          font-weight: 600;
        }

        .loading {
          text-align: center;
          padding: 2rem;
          font-size: 1.2rem;
          color: #6c757d;
        }

        .connection-status {
          text-align: center;
          margin-bottom: 1rem;
          font-size: 1.2rem;
        }
      `}</style>
    </div>
  );
}
