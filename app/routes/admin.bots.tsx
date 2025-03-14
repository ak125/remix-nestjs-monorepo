import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData, useSubmit, Form } from "@remix-run/react";
import { useEffect, useState } from "react";
import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "~/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "~/components/ui/card";
import { Badge } from "~/components/ui/badge";
import { Search, RefreshCw, Plus, Trash2, Info } from "lucide-react";

// Charger la liste des bots au démarrage
export const loader: LoaderFunction = async () => {
  try {
    const apiBaseUrl = process.env.API_BASE_URL || "http://localhost:3000";
    const [botsResponse, statsResponse] = await Promise.all([
      fetch(`${apiBaseUrl}/api/bots`),
      fetch(`${apiBaseUrl}/api/bots/stats`)
    ]);
    
    if (!botsResponse.ok || !statsResponse.ok) {
      throw new Error("Erreur lors de la récupération des données");
    }
    
    const { blockedBots } = await botsResponse.json();
    const { stats } = await statsResponse.json();
    
    return json({ blockedBots, stats });
  } catch (error) {
    console.error("Erreur:", error);
    return json({ 
      blockedBots: [], 
      stats: {
        totalBlocked: 0,
        totalDetections: 0,
        recentDetections: 0,
        lastUpdated: new Date()
      },
      error: "Impossible de charger les données"
    });
  }
};

export default function BotsList() {
  const { blockedBots, stats, error } = useLoaderData<{
    blockedBots: string[];
    stats: {
      totalBlocked: number;
      totalDetections: number;
      recentDetections: number;
      lastUpdated: string;
    };
    error?: string;
  }>();
  
  const [bots, setBots] = useState<string[]>(blockedBots);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState("");
  const [sortOrder, setSortOrder] = useState<"asc" | "desc">("asc");
  const [newBotName, setNewBotName] = useState("");
  const submit = useSubmit();

  // Fonction pour rafraîchir la liste
  const refreshBots = async () => {
    setLoading(true);
    try {
      const apiBaseUrl = process.env.API_BASE_URL || "http://localhost:3000";
      const response = await fetch(`${apiBaseUrl}/api/bots`);
      if (response.ok) {
        const data = await response.json();
        setBots(data.blockedBots);
      }
    } catch (error) {
      console.error("Erreur lors de la récupération des bots:", error);
    } finally {
      setLoading(false);
    }
  };

  // Ajouter un bot à la liste des bloqués
  const handleAddBot = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!newBotName.trim()) return;

    setLoading(true);
    try {
      const formData = new FormData();
      formData.append("_action", "add");
      formData.append("name", newBotName);
      
      submit(formData, { method: "post", action: "/api/admin/bots" });
      
      // Rafraîchir après soumission
      await refreshBots();
      setNewBotName("");
    } catch (error) {
      console.error("Erreur lors de l'ajout du bot:", error);
    } finally {
      setLoading(false);
    }
  };

  // Supprimer un bot
  const handleDeleteBot = async (botName: string) => {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer ${botName} de la liste des bots bloqués?`)) {
      return;
    }

    setLoading(true);
    try {
      const formData = new FormData();
      formData.append("_action", "delete");
      formData.append("name", botName);
      
      submit(formData, { method: "post", action: "/api/admin/bots" });
      
      // Rafraîchir après soumission
      await refreshBots();
    } catch (error) {
      console.error("Erreur lors de la suppression du bot:", error);
    } finally {
      setLoading(false);
    }
  };

  // Filtrer les bots en fonction de la recherche
  const filteredBots = bots
    .filter((bot) => bot.toLowerCase().includes(search.toLowerCase()))
    .sort((a, b) => (sortOrder === "asc" ? a.localeCompare(b) : b.localeCompare(a)));

  return (
    <div className="container mx-auto py-8">
      <h1 className="text-3xl font-bold mb-8">Gestion des bots bloqués</h1>
      
      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
          {error}
        </div>
      )}
      
      {/* Statistiques */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium">Total des bots bloqués</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.totalBlocked}</div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium">Détections totales</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.totalDetections}</div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader className="pb-2">
            <CardTitle className="text-sm font-medium">Détections (24h)</CardTitle>
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.recentDetections}</div>
          </CardContent>
        </Card>
      </div>
      
      {/* Filtre et recherche */}
      <Card className="mb-8">
        <CardHeader>
          <CardTitle className="text-xl">Recherche et filtres</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex flex-col md:flex-row gap-4">
            <div className="relative flex-grow">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={18} />
              <Input
                type="text"
                placeholder="Rechercher un bot par nom..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="pl-10"
              />
            </div>
            <Button
              variant="outline"
              onClick={() => setSortOrder(sortOrder === "asc" ? "desc" : "asc")}
            >
              Trier {sortOrder === "asc" ? "A-Z" : "Z-A"}
            </Button>
            <Button 
              onClick={refreshBots} 
              disabled={loading}
              className="flex items-center gap-2"
            >
              <RefreshCw size={16} className={loading ? "animate-spin" : ""} />
              {loading ? "Chargement..." : "Actualiser"}
            </Button>
          </div>
        </CardContent>
      </Card>
      
      {/* Formulaire d'ajout */}
      <Card className="mb-6">
        <CardHeader>
          <CardTitle className="text-xl">Ajouter un bot à bloquer</CardTitle>
        </CardHeader>
        <CardContent>
          <Form onSubmit={handleAddBot} className="flex gap-4">
            <Input
              type="text"
              placeholder="Nom du bot (ex: SomeBot)"
              value={newBotName}
              onChange={(e) => setNewBotName(e.target.value)}
              className="flex-grow"
            />
            <Button 
              type="submit" 
              disabled={loading || !newBotName.trim()}
              className="flex items-center gap-2"
            >
              <Plus size={16} />
              Ajouter
            </Button>
          </Form>
        </CardContent>
      </Card>
      
      {/* Liste des bots */}
      <Card>
        <CardHeader>
          <CardTitle className="text-xl">Liste des bots bloqués ({filteredBots.length})</CardTitle>
        </CardHeader>
        <CardContent>
          {filteredBots.length > 0 ? (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Nom du bot</TableHead>
                  <TableHead>Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredBots.map((bot, index) => (
                  <TableRow key={index}>
                    <TableCell className="font-medium">{bot}</TableCell>
                    <TableCell>
                      <Button
                        variant="outline"
                        size="sm"
                        className="text-red-500 hover:text-red-700"
                        onClick={() => handleDeleteBot(bot)}
                      >
                        <Trash2 size={16} />
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          ) : (
            <div className="text-center py-10 text-gray-500">
              {search ? "Aucun bot trouvé avec ce terme de recherche." : "Aucun bot bloqué."}
            </div>
          )}
        </CardContent>
      </Card>
      
      <div className="mt-6 p-4 bg-gray-100 rounded-md flex items-center text-sm text-gray-600">
        <Info size={16} className="mr-2" />
        <p>
          Les bots bloqués seront automatiquement exclus des statistiques et ne pourront pas accéder au contenu du site.
          Le fichier robots.txt est également mis à jour automatiquement.
        </p>
      </div>
    </div>
  );
}
