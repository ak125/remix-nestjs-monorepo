import { destroySession } from "~/lib/session.server";

export async function action({ request }) {
  return destroySession(request);
}

export async function loader({ request }) {
  return destroySession(request);
}

export default function Logout() {
  return <div>Déconnexion en cours...</div>;
}
