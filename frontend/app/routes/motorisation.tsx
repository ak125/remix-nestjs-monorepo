import { LoaderFunction, json } from "@remix-run/node";
import { fetchAPI } from "~/utils/api";

interface Motorisation {
  type_id: number;
  type_alias: string;
  type_name: string;
  type_fuel: string;
  type_power_ps: number;
  type_year_from: number;
  type_year_to: number | null;
  type_sort: number;
}

export const loader: LoaderFunction = async ({ request }) => {
  const url = new URL(request.url);
  const params = {
    formGammeid: url.searchParams.get("formGammeid"),
    formCarMarqueid: url.searchParams.get("formCarMarqueid"),
    formCarMarqueYear: url.searchParams.get("formCarMarqueYear"), 
    formCarModelid: url.searchParams.get("formCarModelid")
  };

  if (!Object.values(params).every(Boolean)) {
    return json({ error: "Paramètres manquants" }, { status: 400 });
  }

  try {
    const motorisations = await fetchAPI(`/auto/motorisations?${new URLSearchParams(params)}`);
    return json(motorisations);
  } catch (error) {
    console.error("Erreur récupération motorisations:", error);
    return json({ error: "Erreur serveur" }, { status: 500 });
  }
};
