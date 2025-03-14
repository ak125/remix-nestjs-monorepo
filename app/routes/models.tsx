import { useState } from "react";
import { json, LoaderFunction, ActionFunction, redirect } from "@remix-run/node";
import { useLoaderData, useSearchParams, useSubmit, Form, Link } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { Card, CardContent, CardHeader, CardTitle } from "~/components/ui/card";
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from "~/components/ui/table";
import { Pagination } from "~/components/ui/pagination";
import { 
  ChevronLeft, 
  ChevronRight,
  Search,
  Edit,
  Trash2,
  Plus,
  Filter,
  RefreshCw
} from "lucide-react";

interface Model {
  id: string;
  name: string;
  yearFrom: number;
  yearTo: number | null;
  marque?: {
    id: string;
    name: string;
  };
}

interface LoaderData {
  data: Model[];
  pagination: {
    total: number;
    page: number;
    limit: number;
    totalPages: number;
  };
  error?: string;
}

export const loader: LoaderFunction = async ({ request }): Promise<Response> => {
  const url = new URL(request.url);
  const carMarqueId = url.searchParams.get("carMarqueId") || "";
  const gammeId = url.searchParams.get("gammeId") || "";
  const search = url.searchParams.get("search") || "";
  const page = url.searchParams.get("page") || "1";
  const limit = url.searchParams.get("limit") || "10";

  try {
    // Construction des paramètres
    const params = new URLSearchParams({
      ...(carMarqueId && { carMarqueId }),
      ...(gammeId && { gammeId }),
      ...(search && { search }),
      page,
      limit
    });

    // Appel à l'API NestJS
    const apiBaseUrl = process.env.API_BASE_URL || 'http://localhost:3000';
    const response = await fetch(`${apiBaseUrl}/api/models?${params.toString()}`);

    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }

    const data = await response.json();
    return json(data);
  } catch (error) {
    console.error("Error fetching models:", error);
    return json({ 
      data: [],
      pagination: { page: 1, limit: 10, total: 0, totalPages: 1 },
      error: error instanceof Error ? error.message : "Une erreur est survenue" 
    });
  }
};

export const action: ActionFunction = async ({ request }): Promise<Response> => {
  const formData = await request.formData();
  const action = formData.get("_action");
  const modelId = formData.get("modelId") as string;
  
  if (!modelId) {
    return json({ success: false, message: "ID du modèle manquant" });
  }
  
  if (action === "delete") {
    try {
      // Appel à l'API NestJS pour supprimer
      const apiBaseUrl = process.env.API_BASE_URL || 'http://localhost:3000';
      const response = await fetch(`${apiBaseUrl}/api/models/${modelId}`, {
        method: "DELETE"
      });

      if (!response.ok) {
        const errorData = await response.json();
        return json({
          success: false,
          message: errorData.message || "Erreur lors de la suppression"
        });
      }

      // Rediriger vers la même page (pour rafraîchir les données)
      return redirect(request.url);
    } catch (error) {
      console.error("Error deleting model:", error);
      return json({
        success: false,
        message: "Une erreur est survenue lors de la suppression"
      });
    }
  }
  
  return json({ success: false, message: "Action non supportée" });
};

export default function ModelsList() {
  const { data: models, pagination, error } = useLoaderData<LoaderData>();
  const [searchParams, setSearchParams] = useSearchParams();
  const submit = useSubmit();
  
  const currentPage = parseInt(searchParams.get("page") || "1", 10);
  const currentSearch = searchParams.get("search") || "";
  const currentCarMarqueId = searchParams.get("carMarqueId") || "";
  const currentGammeId = searchParams.get("gammeId") || "";
  
  const [searchValue, setSearchValue] = useState(currentSearch);
  
  // Gestion de la recherche
  const handleSearch = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const newParams = new URLSearchParams(searchParams);
    
    if (searchValue) {
      newParams.set("search", searchValue);
    } else {
      newParams.delete("search");
    }
    
    // Réinitialiser à la page 1 lors d'une nouvelle recherche
    newParams.set("page", "1");
    
    setSearchParams(newParams);
  };
  
  // Gestion de la pagination
  const goToPage = (page: number) => {
    const newParams = new URLSearchParams(searchParams);
    newParams.set("page", page.toString());
    setSearchParams(newParams);
  };
  
  // Gestion de la suppression
  const handleDelete = (modelId: string) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce modèle ?")) {
      const formData = new FormData();
      formData.append("modelId", modelId);
      formData.append("_action", "delete");
      
      submit(formData, { method: "post" });
    }
  };
  
  // Réinitialiser les filtres
  const resetFilters = () => {
    setSearchParams(new URLSearchParams());
    setSearchValue("");
  };
  
  return (
    <div className="container mx-auto py-6">
      <h1 className="text-3xl font-bold mb-6">Liste des Modèles</h1>
      
      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md mb-6">
          {error}
        </div>
      )}
      
      {/* Filtres et recherche */}
      <Card className="mb-6">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Filter className="h-5 w-5" />
            Recherche et filtres
          </CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSearch} className="space-y-4">
            <div className="flex flex-col sm:flex-row gap-4">
              <div className="flex-grow relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={18} />
                <Input 
                  type="text" 
                  placeholder="Rechercher par nom..." 
                  value={searchValue}
                  onChange={(e) => setSearchValue(e.target.value)}
                  className="pl-10"
                />
              </div>
              
              <div className="flex gap-2">
                <Button type="submit">
                  Rechercher
                </Button>
                
                {(currentSearch || currentCarMarqueId || currentGammeId) && (
                  <Button 
                    type="button" 
                    variant="outline" 
                    onClick={resetFilters}
                    className="flex items-center gap-1"
                  >
                    <RefreshCw size={16} />
                    Réinitialiser
                  </Button>
                )}
              </div>
            </div>
          </form>
        </CardContent>
      </Card>
      
      {/* Liste des modèles */}
      <Card>
        <CardHeader>
          <div className="flex justify-between items-center">
            <CardTitle>Modèles ({pagination.total})</CardTitle>
            <Link to="/models/new">
              <Button className="flex items-center gap-1" size="sm">
                <Plus size={16} />
                Nouveau modèle
              </Button>
            </Link>
          </div>
        </CardHeader>
        <CardContent>
          <div className="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>ID</TableHead>
                  <TableHead>Nom</TableHead>
                  <TableHead>Marque</TableHead>
                  <TableHead>Années</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {models.length > 0 ? (
                  models.map((model) => (
                    <TableRow key={model.id}>
                      <TableCell className="font-medium">{model.id}</TableCell>
                      <TableCell>{model.name}</TableCell>
                      <TableCell>{model.marque?.name || "N/A"}</TableCell>
                      <TableCell>
                        {model.yearFrom} - {model.yearTo || "Présent"}
                      </TableCell>
                      <TableCell className="text-right">
                        <div className="flex justify-end gap-2">
                          <Link to={`/models/${model.id}/edit`}>
                            <Button variant="outline" size="sm" className="h-8 w-8 p-0">
                              <Edit className="h-4 w-4" />
                            </Button>
                          </Link>
                          <Button 
                            variant="outline" 
                            size="sm" 
                            className="h-8 w-8 p-0 text-red-500 hover:text-red-700"
                            onClick={() => handleDelete(model.id)}
                          >
                            <Trash2 className="h-4 w-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))
                ) : (
                  <TableRow>
                    <TableCell colSpan={5} className="text-center py-10 text-gray-500">
                      Aucun modèle trouvé
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </div>
          
          {/* Pagination */}
          {pagination.totalPages > 1 && (
            <div className="flex justify-center mt-6">
              <Pagination>
                <Button 
                  variant="outline" 
                  size="sm"
                  disabled={currentPage <= 1}
                  onClick={() => goToPage(1)}
                >
                  Premier
                </Button>
                <Button 
                  variant="outline" 
                  size="sm"
                  disabled={currentPage <= 1}
                  onClick={() => goToPage(currentPage - 1)}
                >
                  <ChevronLeft className="h-4 w-4" />
                </Button>
                
                <span className="px-4 py-2">
                  Page {currentPage} sur {pagination.totalPages}
                </span>
                
                <Button 
                  variant="outline" 
                  size="sm"
                  disabled={currentPage >= pagination.totalPages}
                  onClick={() => goToPage(currentPage + 1)}
                >
                  <ChevronRight className="h-4 w-4" />
                </Button>
                <Button 
                  variant="outline" 
                  size="sm"
                  disabled={currentPage >= pagination.totalPages}
                  onClick={() => goToPage(pagination.totalPages)}
                >
                  Dernier
                </Button>
              </Pagination>
            </div>
          )}
        </CardContent>
      </Card>
    </div>
  );
}
