import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { formatDate } from "~/lib/utils";

export async function loader({ params }: LoaderFunctionArgs) {
  const response = await fetch(
    `${process.env.API_URL}/deliveries/${params.orderId}`,
    { headers: { 'Cache-Control': 'no-cache' } }
  );

  if (!response.ok) {
    throw new Response("Livraison non trouvée", { status: 404 });
  }

  return json(await response.json());
}

export default function TrackingPage() {
  const delivery = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-3xl font-bold mb-8">
        Suivi de Livraison
      </h1>

      <div className="max-w-3xl mx-auto">
        <div className="bg-white rounded-lg shadow-lg overflow-hidden">
          <div className="p-6">
            <div className="flex justify-between items-center mb-6">
              <div>
                <p className="text-sm text-gray-500">Numéro de suivi</p>
                <p className="font-semibold">{delivery.trackingNumber}</p>
              </div>
              <div>
                <p className="text-sm text-gray-500">Transporteur</p>
                <p className="font-semibold">{delivery.carrier}</p>
              </div>
              <div>
                <p className="text-sm text-gray-500">Status</p>
                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                  delivery.status === 'delivered' 
                    ? 'bg-green-100 text-green-800'
                    : 'bg-blue-100 text-blue-800'
                }`}>
                  {delivery.status}
                </span>
              </div>
            </div>

            <div className="space-y-8">
              {delivery.events.map(event => (
                <div key={event.id} className="relative pl-8 pb-8 last:pb-0">
                  <div className="absolute left-0 top-0 mt-1.5">
                    <span className="h-4 w-4 rounded-full bg-blue-200" />
                    {event.id !== delivery.events[delivery.events.length - 1].id && (
                      <span className="absolute top-4 left-2 -ml-px h-full w-0.5 bg-gray-200" />
                    )}
                  </div>
                  <div>
                    <p className="text-sm font-medium text-gray-900">
                      {event.status}
                    </p>
                    <p className="mt-1 text-sm text-gray-500">
                      {event.message}
                    </p>
                    <p className="mt-1 text-xs text-gray-400">
                      {formatDate(event.timestamp)}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
