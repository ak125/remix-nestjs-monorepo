import { LoaderFunctionArgs, json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { formatDate } from "~/lib/utils";

const CARRIER_ICONS = {
  colissimo: "📬",
  chronopost: "⚡",
  dhl: "🌎"
};

const STATUS_BADGES = {
  pending: "bg-yellow-100 text-yellow-800",
  transit: "bg-blue-100 text-blue-800",
  delivered: "bg-green-100 text-green-800",
  failed: "bg-red-100 text-red-800"
};

export async function loader({ params }: LoaderFunctionArgs) {
  const response = await fetch(
    `${process.env.API_URL}/tracking/${params.id}`
  );

  if (!response.ok) {
    throw new Response("Commande non trouvée", { status: 404 });
  }

  return json(await response.json());
}

export default function TrackingPage() {
  const { tracking, order } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <div className="max-w-2xl mx-auto">
        <h1 className="text-3xl font-bold mb-8">
          Suivi de commande {order.reference}
        </h1>

        <div className="bg-white rounded-lg shadow-lg p-6 mb-8">
          <div className="flex items-center justify-between mb-4">
            <div>
              <span className="text-2xl mr-2">
                {CARRIER_ICONS[tracking.carrier]}
              </span>
              <span className="font-semibold">
                {tracking.carrier.toUpperCase()}
              </span>
            </div>
            <span className={`px-3 py-1 rounded-full text-sm ${
              STATUS_BADGES[tracking.status]
            }`}>
              {tracking.status}
            </span>
          </div>

          <p className="text-gray-600 mb-4">
            Livraison prévue le {formatDate(tracking.estimatedDelivery)}
          </p>

          <div className="space-y-4">
            {tracking.history.map((event, index) => (
              <div 
                key={index}
                className="flex items-start space-x-4"
              >
                <div className="w-2 h-2 mt-2 rounded-full bg-blue-500" />
                <div>
                  <p className="font-medium">{event.status}</p>
                  <p className="text-sm text-gray-500">
                    {formatDate(event.date)}
                  </p>
                  <p className="text-sm text-gray-600">
                    {event.location}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
