import { destroyAdminSession } from "~/lib/admin-session.server";

export async function loader({ request }) {
  return destroyAdminSession(request);
}

export async function action({ request }) {
  return destroyAdminSession(request);
}

export default function AdminLogout() {
  return <p>Redirection...</p>;
}
