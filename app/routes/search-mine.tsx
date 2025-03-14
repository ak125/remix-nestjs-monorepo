import { json, LoaderFunctionArgs } from "@remix-run/node";
import { useLoaderData, Form } from "@remix-run/react";
import { redirect } from "@remix-run/node";
import { prisma } from "~/lib/prisma.server";

async function fetchData(url: string) {
  const response = await fetch(url);
  if (!response.ok) {
    throw new Error('Network response was not ok');
  }
  return response.json();
}

export async function loader({ request }: LoaderFunctionArgs) {
  const url = new URL(request.url);
  const mine = url.searchParams.get("mine");
  const ask2page = url.searchParams.get("ask2page") || "1";
  const gamme = url.searchParams.get("gamme");

  if (!mine) {
    return json({});
  }

  const vehicle = await prisma.autoType.findFirst({
    where: {
      AUTO_TYPE_NUMBER_CODE: {
        some: {
          TNC_CNIT: mine
        }
      },
      TYPE_DISPLAY: true
    },
    include: {
      AUTO_MODELE: {
        include: {
          AUTO_MARQUE: true
        }
      }
    }
  });

  if (!vehicle) {
    return redirect("/type-mine?error=not-found");
  }

  if (ask2page === "1") {
    return redirect(`/auto/${vehicle.AUTO_MODELE.AUTO_MARQUE.MARQUE_ALIAS}-${vehicle.AUTO_MODELE.AUTO_MARQUE.MARQUE_ID}/${vehicle.AUTO_MODELE.MODELE_ALIAS}-${vehicle.AUTO_MODELE.MODELE_ID}/${vehicle.TYPE_ALIAS}-${vehicle.TYPE_ID}`);
  }

  if (ask2page === "2" && gamme) {
    return redirect(`/pieces/${gamme}/${vehicle.AUTO_MODELE.AUTO_MARQUE.MARQUE_ALIAS}-${vehicle.AUTO_MODELE.AUTO_MARQUE.MARQUE_ID}/${vehicle.AUTO_MODELE.MODELE_ALIAS}-${vehicle.AUTO_MODELE.MODELE_ID}/${vehicle.TYPE_ALIAS}-${vehicle.TYPE_ID}`);
  }

  return json({ error: "Configuration invalide" });
}

export default function SearchMinePage() {
  const { error } = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto py-8 px-4">
      <h1 className="text-2xl font-bold mb-8">
        Recherche par Type Mine
      </h1>

      <Form method="get" className="max-w-md">
        <div className="space-y-4">
          <input
            type="text"
            name="mine"
            placeholder="Entrez le numéro de type mine"
            className="w-full px-4 py-2 border rounded-lg"
          />

          <select 
            name="ask2page"
            className="w-full px-4 py-2 border rounded-lg"
          >
            <option value="1">Voir le véhicule</option>
            <option value="2">Voir les pièces</option>
          </select>

          <button
            type="submit"
            className="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Rechercher
          </button>
        </div>
      </Form>

      {error && (
        <p className="mt-4 text-red-500">
          {error}
        </p>
      )}
    </div>
  );
}
