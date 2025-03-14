import { LoaderFunction } from "@remix-run/node";
import { requireAdmin } from "~/utils/auth.server";
import { prisma } from "~/utils/db.server";

export const loader: LoaderFunction = async ({ request }) => {
  await requireAdmin(request);

  const stream = new TransformStream();
  const writer = stream.writable.getWriter();
  const encoder = new TextEncoder();

  let lastCheck = new Date();

  const interval = setInterval(async () => {
    try {
      const notifications = await prisma.notification.findMany({
        where: {
          createdAt: { gt: lastCheck },
          isRead: false,
        },
        orderBy: { createdAt: "desc" },
      });

      if (notifications.length > 0) {
        const chunk = encoder.encode(`data: ${JSON.stringify(notifications)}\n\n`);
        await writer.write(chunk);
        lastCheck = new Date();
      }
    } catch (error) {
      console.error("Stream error:", error);
    }
  }, 3000);

  request.signal.addEventListener("abort", () => {
    clearInterval(interval);
    writer.close();
  });

  return new Response(stream.readable, {
    headers: {
      "Content-Type": "text/event-stream",
      "Cache-Control": "no-cache",
      "Connection": "keep-alive",
    },
  });
};
