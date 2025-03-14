import { json, LoaderFunction } from "@remix-run/node";
import { useLoaderData, Link, useSearchParams } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "~/components/ui/card";
import { Input } from "~/components/ui/input";
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from "~/components/ui/table";
import { requireUserRole } from "~/services/auth.server";
import { 
  ChevronLeft, 
  ChevronRight, 
  Edit, 
  Eye, 
  PlusCircle, 
  Search, 
  Trash2,
  Filter,
  X,
  Calendar 
} from "lucide-react";
import { Pagination } from "~/components/ui/pagination";
import { useEffect, useState } from "react";
import { fetchModeles } from "~/services/modele.server";
import { Marque, Modele, ModeleFilters, PaginatedResponse } from "~/types/modele.types";
import { fetchAllMarques } from "~/services/marque.server";

interface LoaderData {
  modeles: Modele[];
  pagination: PaginatedResponse<Modele>["pagination"];
  marques: Marque[];
  years: number[];
  filters: ModeleFilters;
  error?: string;
}

export const loader: LoaderFunction = async ({ request }): Promise<Response> => {
  // Vérifier que l'utilisateur est administrateur
  await requireUserRole(request, ["ADMIN"]);
  
  const url = new URL(request.url);
  const filters: ModeleFilters = {
    page: url.searchParams.get("page") || "1",
    limit: url.searchParams.get("limit") || "25",
    search: url.searchParams.get("search") || "",
    marqueId: url.searchParams.get("marqueId") || "",
    yearFrom: url.searchParams.get("yearFrom") || "",
    yearTo: url.searchParams.get("yearTo") || "",
  };
  
  try {
    // Récupérer les données en parallèle pour optimiser les performances
    const [modelesResponse, marques] = await Promise.all([
      fetchModeles(filters),
      fetchAllMarques()
    ]);
    
    // Générer les années pour le filtre (de 1900 à l'année en cours)
    const currentYear = new Date().getFullYear();
    const years = Array.from({ length: currentYear - 1899 }, (_, i) => 1900 + i);
    
    return json<LoaderData>({
      modeles: modelesResponse.data,
      pagination: modelesResponse.pagination,
      marques,
      years,
      filters
    });
  } catch (error) {
    console.error("Error loading data:", error);
    return json<LoaderData>({ 
      error: error instanceof Error ? error.message : "Une erreur est survenue lors du chargement des données",
      modeles: [],
      pagination: { page: 1, limit: 25, total: 0, totalPages: 1 },
      marques: [],
      years: [],
      filters
    });
  }
};

export default function ModelesAdmin() {
  const { modeles, pagination, marques, years, filters, error } = useLoaderData<LoaderData>();
  const [searchParams, setSearchParams] = useSearchParams();
  const [showAdvancedFilters, setShowAdvancedFilters] = useState(false);
  
  const currentPage = parseInt(searchParams.get("page") || "1");
  
  // Vérifier si des filtres sont actifs
  const hasActiveFilters = !!filters.search || !!filters.marqueId || !!filters.yearFrom || !!filters.yearTo;
  
  // Activer les filtres avancés automatiquement si des filtres sont appliqués
  useEffect(() => {
    if (filters.marqueId || filters.yearFrom || filters.yearTo) {
      setShowAdvancedFilters(true);
    }
  }, [filters.marqueId, filters.yearFrom, filters.yearTo]);
  
  // Mettre à jour les paramètres de recherche
  const handleFormSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    
    const newParams = new URLSearchParams();
    const search = formData.get("search") as string;
    const marqueId = formData.get("marqueId") as string;
    const yearFrom = formData.get("yearFrom") as string;
    const yearTo = formData.get("yearTo") as string;
    
    if (search) newParams.set("search", search);
    if (marqueId) newParams.set("marqueId", marqueId);
    if (yearFrom) newParams.set("yearFrom", yearFrom);
    if (yearTo) newParams.set("yearTo", yearTo);
    
    // Retour à la première page sur nouvelle recherche
    newParams.set("page", "1");
    
    setSearchParams(newParams);
  };
  
  // Réinitialiser tous les filtres
  const handleResetFilters = () => {
    setSearchParams(new URLSearchParams());
  };
  
  // Changer de page
  const goToPage = (page: number) => {
    const newParams = new URLSearchParams(searchParams);
    newParams.set("page", page.toString());
    setSearchParams(newParams);
  };
  
  return (
    <div className="container py-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold">Gestion des modèles</h1>
        <Link to="/admin/modeles/new">
          <Button className="flex items-center gap-2">
            <PlusCircle className="h-4 w-4" /> Nouveau modèle
          </Button>
        </Link>
      </div>
      
      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md mb-6">
          {error}
        </div>
      )}
      
      <Card className="mb-6">
        <CardHeader className="pb-0">
          <div className="flex justify-between items-center">
            <CardTitle className="text-lg">Recherche</CardTitle>
            <div className="flex gap-2">
              <Button 
                variant="outline" 
                size="sm" 
                onClick={() => setShowAdvancedFilters(!showAdvancedFilters)}
                className="text-xs"
              >
                {showAdvancedFilters ? (
                  <>
                    <X className="h-3.5 w-3.5 mr-1" />
                    Filtres simples
                  </>
                ) : (
                  <>
                    <Filter className="h-3.5 w-3.5 mr-1" />
                    Filtres avancés
                  </>
                )}
              </Button>
              
              {hasActiveFilters && (
                <Button 
                  variant="ghost" 
                  size="sm" 
                  onClick={handleResetFilters}
                  className="text-xs"
                >
                  Réinitialiser
                </Button>
              )}
            </div>
          </div>
        </CardHeader>
        <CardContent className="pt-4">
          <form onSubmit={handleFormSubmit} className="space-y-4">
            {/* Barre de recherche principale */}
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
              <Input 
                name="search" 
                placeholder="Rechercher par nom de modèle..." 
                className="pl-10"
                defaultValue={filters.search || ""}
              />
            </div>
            
            {/* Filtres avancés */}
            {showAdvancedFilters && (
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div className="space-y-2">
                  <label htmlFor="marqueId" className="text-sm font-medium">Marque</label>
                  <select 
                    id="marqueId" 
                    name="marqueId" 
                    className="w-full p-2 border rounded"
                    defaultValue={filters.marqueId || ""}
                  >
                    <option value="">Toutes les marques</option>
                    {marques.map(marque => (
                      <option key={marque.id} value={marque.id}>
                        {marque.name}
                      </option>
                    ))}
                  </select>
                </div>
                
                <div className="space-y-2">
                  <label htmlFor="yearFrom" className="text-sm font-medium">Année début</label>
                  <select 
                    id="yearFrom" 
                    name="yearFrom" 
                    className="w-full p-2 border rounded"
                    defaultValue={filters.yearFrom || ""}
                  >
                    <option value="">Toutes</option>
                    {years.slice().reverse().map(year => (
                      <option key={`from-${year}`} value={year}>
                        {year}
                      </option>
                    ))}
                  </select>
                </div>
                
                <div className="space-y-2">
                  <label htmlFor="yearTo" className="text-sm font-medium">Année fin</label>
                  <select 
                    id="yearTo" 
                    name="yearTo" 
                    className="w-full p-2 border rounded"
                    defaultValue={filters.yearTo || ""}
                  >
                    <option value="">Toutes</option>
                    {years.slice().reverse().map(year => (
                      <option key={`to-${year}`} value={year}>
                        {year}
                      </option>
                    ))}
                  </select>
                </div>
              </div>
            )}
            
            <div className="flex justify-end">
              <Button type="submit" className="flex items-center">
                <Search className="h-4 w-4 mr-2" />
                Rechercher
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
      
      {/* Résultats et pagination */}
      <div className="mb-4 flex justify-between items-center text-sm text-gray-500">
        <span>
          {pagination.total} modèles trouvés
        </span>
        
        <span>
          Page {pagination.page} sur {pagination.totalPages}
        </span>
      </div>
      
      <Card className="mb-6">
        <CardContent className="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>ID</TableHead>
                <TableHead>Nom</TableHead>
                <TableHead>Marque</TableHead>
                <TableHead>
                  <div className="flex items-center">
                    <Calendar className="h-4 w-4 mr-1" />
                    Années
                  </div>
                </TableHead>
                <TableHead>Actif</TableHead>
                <TableHead className="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {modeles.length > 0 ? (
                modeles.map((modele) => (
                  <TableRow key={modele.id}>
                    <TableCell>{modele.id}</TableCell>
                    <TableCell className="font-medium">{modele.name}</TableCell>
                    <TableCell>{modele.marque?.name || "N/A"}</TableCell>
                    <TableCell>
                      {modele.yearFrom} - {modele.yearTo || "Présent"}
                    </TableCell>
                    <TableCell>
                      <span className={`inline-block w-3 h-3 rounded-full ${modele.display ? 'bg-green-500' : 'bg-red-500'}`}></span>
                    </TableCell>
                    <TableCell className="text-right space-x-2">
                      <Link to={`/admin/modeles/${modele.id}/view`}>
                        <Button variant="ghost" size="icon">
                          <Eye className="h-4 w-4" />
                        </Button>
                      </Link>
                      <Link to={`/admin/modeles/${modele.id}/edit`}>
                        <Button variant="ghost" size="icon">
                          <Edit className="h-4 w-4" />
                        </Button>
                      </Link>
                      <Link to={`/admin/modeles/${modele.id}/delete`}>
                        <Button variant="ghost" size="icon">
                          <Trash2 className="h-4 w-4 text-red-500" />
                        </Button>
                      </Link>
                    </TableCell>
                  </TableRow>
                ))
              ) : (
                <TableRow>
                  <TableCell colSpan={6} className="text-center py-8 text-gray-500">
                    {hasActiveFilters ? 
                      "Aucun modèle ne correspond à vos critères de recherche" :
                      "Aucun modèle trouvé dans la base de données"
                    }
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
      
      {/* Pagination améliorée */}
      {pagination.totalPages > 1 && (
        <div className="flex justify-center">
          <Pagination>
            <Button 
              variant="outline" 
              onClick={() => goToPage(1)}
              disabled={currentPage <= 1}
              className="hidden md:flex mr-1"
              size="sm"
            >
              Premier
            </Button>
            
            <Button 
              variant="outline" 
              onClick={() => goToPage(currentPage - 1)}
              disabled={currentPage <= 1}
              className="mr-1"
              size="sm"
            >
              <ChevronLeft className="h-4 w-4" />
            </Button>
            
            {/* Affichage des numéros de page */}
            <div className="hidden md:flex items-center gap-1 mx-2">
              {Array.from({ length: Math.min(5, pagination.totalPages) }, (_, i) => {
                // Logique pour afficher les pages autour de la page courante
                let pageNum;
                if (pagination.totalPages <= 5) {
                  pageNum = i + 1;
                } else if (currentPage <= 3) {
                  pageNum = i + 1;
                } else if (currentPage >= pagination.totalPages - 2) {
                  pageNum = pagination.totalPages - 4 + i;
                } else {
                  pageNum = currentPage - 2 + i;
                }
                
                return (
                  <Button
                    key={pageNum}
                    variant={currentPage === pageNum ? "default" : "outline"}
                    size="sm"
                    onClick={() => goToPage(pageNum)}
                    className="w-8 h-8 p-0"
                  >
                    {pageNum}
                  </Button>
                );
              })}
            </div>
            
            {/* Version mobile: Afficher simplement le numéro de page courant */}
            <span className="flex md:hidden items-center mx-2 text-sm">
              Page {currentPage} sur {pagination.totalPages}
            </span>
            
            <Button 
              variant="outline" 
              onClick={() => goToPage(currentPage + 1)}
              disabled={currentPage >= pagination.totalPages}
              className="ml-1"
              size="sm"
            >
              <ChevronRight className="h-4 w-4" />
            </Button>
            
            <Button 
              variant="outline" 
              onClick={() => goToPage(pagination.totalPages)}
              disabled={currentPage >= pagination.totalPages}
              className="hidden md:flex ml-1"
              size="sm"
            >
              Dernier
            </Button>
          </Pagination>
        </div>
      )}
      
      {/* Message d'aide en bas de page */}
      {hasActiveFilters && pagination.total === 0 && (
        <div className="mt-8 text-center">
          <p className="text-gray-500">
            Aucun résultat trouvé avec les filtres actuels.{' '}
            <Button variant="link" className="p-0 h-auto" onClick={handleResetFilters}>
              Réinitialiser les filtres
            </Button>
          </p>
        </div>
      )}
    </div>
  );
}
