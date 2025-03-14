import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { prisma } from "~/utils/db.server";

export const loader: LoaderFunction = async () => {
  const errors = await prisma.notificationError.findMany({
    orderBy: { lastAttempt: "desc" },
  });

  const logs = await prisma.sitemapNotificationLog.findMany({
    where: {
      statusCode: {
        gte: 400
      }
    },
    orderBy: { timestamp: "desc" },
    take: 20,
  });

  return json({ errors, logs });
};

export default function SitemapErrorsPage() {
  const { errors, logs } = useLoaderData<typeof loader>();

  return (
    <div>
      <h1>🚨 Erreurs de Notification Sitemap</h1>
      
      <h2>Moteurs avec Erreurs Persistantes</h2>
      {errors.length === 0 ? (
        <p>Aucune erreur persistante détectée.</p>
      ) : (
        <table style={{ width: "100%", borderCollapse: "collapse" }}>
          <thead>
            <tr>
              <th>Moteur</th>
              <th>Nombre d'échecs</th>
              <th>Dernière tentative</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            {errors.map((error) => (
              <tr key={error.id}>
                <td>{error.engine}</td>
                <td>{error.failureCount}</td>
                <td>{new Date(error.lastAttempt).toLocaleString()}</td>
                <td>
                  {error.failureCount >= 3 ? (
                    <span style={{ color: "red" }}>⚠️ Alerte envoyée</span>
                  ) : (
                    <span style={{ color: "orange" }}>⚠️ En surveillance</span>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
      
      <h2>Dernières Erreurs</h2>
      {logs.length === 0 ? (
        <p>Aucune erreur récente.</p>
      ) : (
        <table style={{ width: "100%", borderCollapse: "collapse", marginTop: "20px" }}>
          <thead>
            <tr>
              <th>Moteur</th>
              <th>Code</th>
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
      )}
    </div>
  );
}
