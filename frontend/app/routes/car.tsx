import { useLoaderData } from "@remix-run/react";
import { json } from "@remix-run/node";
import { fetchAPI } from "~/utils/api";
import CarInfo from "~/components/CarInfo";

export const loader = async ({ request }: { request: Request }) => {
  const url = new URL(request.url);
  const marqueId = url.searchParams.get("marqueId") || "1";
  const modeleId = url.searchParams.get("modeleId") || "2"; 
  const typeId = url.searchParams.get("typeId") || "3";

  const data = await fetchAPI(`/auto/details?marque_id=${marqueId}&modele_id=${modeleId}&type_id=${typeId}`);
  return json(data);
};

export default function CarPage() {
  const car = useLoaderData<typeof loader>();

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold">Informations sur la voiture</h1>
      <CarInfo car={car} />
    </div>
  );
}
