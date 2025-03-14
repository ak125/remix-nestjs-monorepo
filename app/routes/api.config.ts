import { json } from "@remix-run/node";
import { ConfigService } from "~/services/config.server";

export async function loader({ request }) {
  const url = new URL(request.url);
  const language = parseInt(url.searchParams.get("lang") || "1", 10);
  
  try {
    const configService = new ConfigService();
    const config = await configService.getSiteConfig(language);
    
    return json(config);
  } catch (error) {
    console.error("Configuration error:", error);
    return json({ error: "Failed to load site configuration" }, { status: 500 });
  }
}
