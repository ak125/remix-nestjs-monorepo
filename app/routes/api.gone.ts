import { json } from "@remix-run/node";

export const loader = async () => {
  return json(
    { message: "Cette page a été supprimée ou déplacée définitivement" }, 
    { status: 410 }
  );
};
