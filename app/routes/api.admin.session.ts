import { json } from "@remix-run/node";
import { getAdminFromSession } from "~/lib/admin-session.server";

export async function loader({ request }) {
  const admin = await getAdminFromSession(request);
  
  if (!admin) {
    return json({ loggedIn: false }, { status: 401 });
  }
  
  return json({ 
    loggedIn: true, 
    user: {
      id: admin.id,
      login: admin.login,
      level: admin.level,
      firstName: admin.firstName,
      lastName: admin.lastName
    }
  });
}
