import { json, redirect } from "@remix-run/node";
import { useLoaderData, Link } from "@remix-run/react";
import { requireAdmin } from "~/lib/admin-session.server";
import { AdminLayout } from "~/components/admin/admin-layout";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "~/components/ui/card";
import { Button } from "~/components/ui/button";
import { 
  LineChart, 
  BarChart, 
  Users, 
  ShoppingCart, 
  Search,
  ArrowUpRight
} from "lucide-react";
import { prisma } from "~/lib/db.server";

export async function loader({ request }) {
  const admin = await requireAdmin(request);
  
  // Dashboard stats
  const [
    totalUsers,
    totalOrders,
    todayOrders,
    totalProducts
  ] = await Promise.all([
    prisma.user.count(),
    prisma.order.count(),
    prisma.order.count({
      where: {
        createdAt: {
          gte: new Date(new Date().setHours(0, 0, 0, 0))
        }
      }
    }),
    0 // Replace with actual product count when implemented
  ]);
  
  return json({
    admin,
    stats: {
      totalUsers,
      totalOrders,
      todayOrders,
      totalProducts
    }
  });
}

export default function AdminDashboardPage() {
  const { admin, stats } = useLoaderData<typeof loader>();
  
  return (
    <AdminLayout>
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-3xl font-bold">Tableau de Bord</h1>
        <div className="text-sm text-muted-foreground">
          Bienvenue, {admin.firstName || admin.login} (Niveau {admin.level})
        </div>
      </div>
      
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">
              Utilisateurs
            </CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.totalUsers}</div>
            <p className="text-xs text-muted-foreground">
              Clients enregistrés
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">
              Commandes
            </CardTitle>
            <ShoppingCart className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.totalOrders}</div>
            <p className="text-xs text-muted-foreground">
              {stats.todayOrders} aujourd'hui
            </p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">
              Produits
            </CardTitle>
            <Search className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.totalProducts}</div>
            <p className="text-xs text-muted-foreground">
              Dans le catalogue
            </p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">
              SEO
            </CardTitle>
            <LineChart className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">Actif</div>
            <p className="text-xs text-muted-foreground">
              Dernière mise à jour: aujourd'hui
            </p>
          </CardContent>
        </Card>
      </div>
      
      <div className="mt-8 grid gap-6 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Référencement, Sitemaps, Keywords...</CardTitle>
            <CardDescription>
              Gérez les aspects SEO de votre site
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <p>
              Optimisez votre positionnement dans les moteurs de recherche en gérant vos mots-clés, URLs et sitemaps.
            </p>
            
            <div className="flex flex-wrap gap-2">
              <Button asChild variant="outline" size="sm">
                <Link to="/admin/seo/keywords">
                  <span className="mr-1">Mots-clés</span>
                  <ArrowUpRight className="h-3 w-3" />
                </Link>
              </Button>
              <Button asChild variant="outline" size="sm">
                <Link to="/admin/seo/sitemaps">
                  <span className="mr-1">Sitemaps</span>
                  <ArrowUpRight className="h-3 w-3" />
                </Link>
              </Button>
              <Button asChild variant="outline" size="sm">
                <Link to="/admin/seo/urls">
                  <span className="mr-1">URLs</span>
                  <ArrowUpRight className="h-3 w-3" />
                </Link>
              </Button>
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader>
            <CardTitle>Vos dernières actions</CardTitle>
            <CardDescription>
              Activités récentes réalisées sur la plateforme
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="text-sm space-y-3">
              <div className="flex justify-between items-center border-b pb-2">
                <span>Connexion au système</span>
                <span className="text-muted-foreground">Aujourd'hui</span>
              </div>
              <div className="flex justify-between items-center border-b pb-2">
                <span>Mise à jour du sitemap</span>
                <span className="text-muted-foreground">Il y a 3 jours</span>
              </div>
              <div className="flex justify-between items-center">
                <span>Modification des mots-clés</span>
                <span className="text-muted-foreground">Il y a 5 jours</span>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </AdminLayout>
  );
}
