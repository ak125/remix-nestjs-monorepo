import { json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { requireAdmin } from "~/lib/admin-session.server";
import { prisma } from "~/lib/db.server";
import { PageZTable } from "~/components/admin/pagez/pagez-table";
import { Input } from "~/components/ui/input";
import { Button } from "~/components/ui/button";
import { Search, Plus, Database } from "lucide-react";
import { useState } from "react";

export async function loader({ request }) {
  // Vérifier que l'utilisateur est administrateur avec niveau >= 7
  await requireAdmin(request, 7);
  
  try {
    const pages = await prisma.pageZ.findMany({
      orderBy: [
        { mfName: 'asc' },
        { pgName: 'asc' },
      ],
    });
    
    return json({ pages });
  } catch (error) {
    console.error("Error loading PageZ data:", error);
    return json({ pages: [], error: "Une erreur est survenue lors de la récupération des données" });
  }
}

export default function AdminPageZ() {
  const { pages, error } = useLoaderData<typeof loader>();
  const [searchTerm, setSearchTerm] = useState("");
  
  // Filtrer les pages selon le terme de recherche
  const filteredPages = pages.filter(page => 
    page.pgName.toLowerCase().includes(searchTerm.toLowerCase()) ||
    page.mfName.toLowerCase().includes(searchTerm.toLowerCase()) ||
    page.pgId.toString().includes(searchTerm) ||
    page.mfId.toString().includes(searchTerm)
  );
  
  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="text-2xl font-bold">Pages Z</h1>
          <p className="text-muted-foreground">
            Gestionnaire de contenu et référencement pour les pages Z
          </p>
        </div>
        <Button 
          className="flex items-center" 
          variant="outline"
          disabled
        >
          <Plus className="w-4 h-4 mr-2" />
          Nouvelle page
        </Button>
      </div>
      
      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md mb-6">
          {error}
        </div>
      )}
      
      <div className="flex items-center justify-between mb-6">
        <div className="flex items-center gap-2">
          <Database className="text-muted-foreground" />
          <span>{pages.length} entrées disponibles</span>
        </div>
        
        <div className="relative w-64">
          <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
          <Input
            placeholder="Rechercher..."
            className="pl-8"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
      </div>
      
      <PageZTable pages={filteredPages} />
    </div>
  );
}
