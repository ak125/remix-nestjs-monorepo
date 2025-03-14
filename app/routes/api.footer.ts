import { json } from "@remix-run/node";
import { FooterService } from "~/services/footer.server";

export async function loader() {
  try {
    const footerService = new FooterService();
    const menus = await footerService.getFooterMenus();
    
    return json(menus);
  } catch (error) {
    console.error("Failed to load footer menus:", error);
    return json({ error: "Failed to load footer menus" }, { status: 500 });
  }
}
