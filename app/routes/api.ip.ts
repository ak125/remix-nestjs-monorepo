import { json } from "@remix-run/node";
import { IPConfigService } from "~/services/ip-config.server";

export async function loader({ request }) {
  const url = new URL(request.url);
  const alias = url.searchParams.get("alias");
  
  try {
    const ipService = new IPConfigService();
    
    if (alias) {
      const ipValue = await ipService.getIP(alias);
      
      if (!ipValue) {
        return json({ error: "IP alias not found" }, { status: 404 });
      }
      
      return json({ [alias]: ipValue });
    }
    
    const ipConfigs = await ipService.getIPConfigs();
    return json(ipConfigs);
  } catch (error) {
    console.error("IP configuration error:", error);
    return json({ error: "Failed to load IP configurations" }, { status: 500 });
  }
}
