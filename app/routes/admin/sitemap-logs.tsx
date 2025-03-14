import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { prisma } from "~/utils/db.server";

export const loader: LoaderFunction = async () => {
  const logs = await prisma.sitemapNotificationLog.findMany({
    orderBy: { timestamp: "desc" },
    take: 20, // Limite aux 20 derniers logs
  });

  return json(logs);
};

export default function SitemapLogsPage() {
  const logs = useLoaderData<typeof loader>();

  return (
    <div>
      <h1>📜 Logs des Notifications Sitemap</h1>
      <table>
        <thead>
          <tr>
            <th>Moteur</th>
            <th>Statut</th>
            <th>Message</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          {logs.map((log) => (
            <tr key={log.id}>
              <td>{log.engine}</td>
              <td>{log.statusCode}</td>
              <td>{log.message}</td>
              <td>{new Date(log.timestamp).toLocaleString()}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
