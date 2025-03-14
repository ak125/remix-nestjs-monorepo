import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import { requireAdmin } from "~/utils/auth.server";

export async function loader({ request }) {
  await requireAdmin(request);
  
  const metrics = await prisma.seoMetrics.findMany({
    orderBy: { date: 'desc' },
    take: 100,
    select: {
      pageUrl: true,
      clicks: true,
      impressions: true,
      ctr: true,
      position: true,
      date: true
    }
  });

  return json(metrics);
}
