import { redirect } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import { cache } from "~/lib/cache.server";

export async function checkGoneUrls(request: Request) {
  const url = new URL(request.url);
  const path = url.pathname;
  
  // Skip checking for API routes and asset routes
  if (path.startsWith('/api/') || path.startsWith('/assets/')) {
    return null;
  }
  
  // Check cache first
  const cacheKey = `gone-url:${path}`;
  const cachedResult = await cache.get<boolean>(cacheKey);
  
  if (cachedResult === true) {
    throw redirect('/410');
  } else if (cachedResult === false) {
    return null;
  }
  
  // Check database
  const goneUrl = await prisma.goneUrl.findUnique({
    where: { path }
  });
  
  // Cache the result for better performance
  await cache.set(cacheKey, !!goneUrl, 3600); // Cache for 1 hour
  
  if (goneUrl) {
    throw redirect('/410');
  }
  
  return null;
}
