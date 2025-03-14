import { redirect } from "@remix-run/node";
import { logout } from "~/lib/session.server";

export async function loader({ request }) {
  return logout(request);
}

export default function LogoutRoute() {
  // This page won't be rendered because we'll redirect away from it
  return null;
}
