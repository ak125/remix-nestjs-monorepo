import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export async function loader() {
  try {
    // Check cache first for better performance
    const cachedMenus = await cache.get<any[]>('footer-menus');
    if (cachedMenus) {
      return json(cachedMenus);
    }

    const menus = await prisma.footerMenu.findMany({
      where: { 
        active: true 
      },
      orderBy: [
        { level: 'asc' },
        { position: 'asc' }
      ],
    });

    // Cache the results for 1 hour
    await cache.set('footer-menus', menus, 3600);
    
    return json(menus);
  } catch (error) {
    console.error("Error fetching footer menus:", error);
    return json({ error: "Server error" }, { status: 500 });
  }
}
