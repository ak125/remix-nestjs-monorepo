import { useState } from "react";
import { json } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { Card, CardContent, CardHeader, CardTitle } from "~/components/ui/card";
import { Label } from "~/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "~/components/ui/select";
import { Button } from "~/components/ui/button";
import { AlertCircle, Calendar, Car, Database } from "lucide-react";
import { prisma } from "~/lib/db.server";
import { requireUserRole } from "~/lib/session.server";

export async function loader({ request }) {
  // Vérifier que l'utilisateur est administrateur
  await requireUserRole(request, ["ADMIN"]);
  
  try {
    // Charger les familles et leurs gammes associées
    const families = await prisma.catalogFamily.findMany({
      where: { display: true },
      select: {
        id: true,
        name: true,
        gammes: {
          include: {
            gamme: {
              select: {
                id: true,
                name: true
              }
            }
          },
          where: {
            gamme: {
              display: true
            }
          }
        }
      },
      orderBy: {
        sort: 'asc'
      }
    });

    // Charger toutes les marques
    const marques = await prisma.marque.findMany({
      where: { display: true },
      orderBy: { sort: 'asc' }
    });
    
    return json({ families, marques });
  } catch (error) {
    console.error("Error loading data:", error);
    return json({ error: "Une erreur est survenue lors du chargement des données" });
  }
}

export default function AdminModeles() {
  const { families, marques, error } = useLoaderData<typeof loader>();
  
  const [selectedFamily, setSelectedFamily] = useState<string | null>(null);
  const [selectedGamme, setSelectedGamme] = useState<string | null>(null);
  const [selectedMarque, setSelectedMarque] = useState<string | null>(null);
  const [years, setYears] = useState<Array<{value: number, label: string, favorite: boolean}>>([]);
  const [modeles, setModeles] = useState<Array<any>>([]);
  const [selectedYear, setSelectedYear] = useState<string | null>(null);
  const [loading, setLoading] = useState<boolean>(false);
  
  // Filtrer les gammes en fonction de la famille sélectionnée
  const gammes = selectedFamily 
    ? families.find(f => f.id.toString() === selectedFamily)?.gammes.map(g => g.gamme) || []
    : [];

  // Charger les années disponibles
  async function loadYears() {
    if (!selectedGamme || !selectedMarque) return;
    
    setLoading(true);
    
    try {
      const response = await fetch(`/api/modele/annees?gammeId=${selectedGamme}&marqueId=${selectedMarque}`);
      const data = await response.json();
      
      if (data.error) {
        console.error(data.error);
        setYears([]);
      } else {
        setYears(data);
      }
    } catch (error) {
      console.error("Error fetching years:", error);
      setYears([]);
    } finally {
      setLoading(false);
    }
  }
  
  // Charger les modèles disponibles
  async function loadModeles() {
    if (!selectedGamme || !selectedMarque || !selectedYear) return;
    
    setLoading(true);
    
    try {
      const response = await fetch(
        `/api/modele/selection?gammeId=${selectedGamme}&marqueId=${selectedMarque}&year=${selectedYear}`
      );
      const data = await response.json();
      
      if (data.error) {
        console.error(data.error);
        setModeles([]);
      } else {
        setModeles(data);
      }
    } catch (error) {
      console.error("Error fetching modeles:", error);
      setModeles([]);
    } finally {
      setLoading(false);
    }
  }

  // Gérer les modifications de sélection
  function handleFamilyChange(value: string) {
    setSelectedFamily(value);
    setSelectedGamme(null);
    setSelectedYear(null);
    setYears([]);
    setModeles([]);
  }
  
  function handleGammeChange(value: string) {
    setSelectedGamme(value);
    setSelectedYear(null);
    setYears([]);
    setModeles([]);
    if (selectedMarque) {
      loadYears();
    }
  }
  
  function handleMarqueChange(value: string) {
    setSelectedMarque(value);
    setSelectedYear(null);
    setYears([]);
    setModeles([]);
    if (selectedGamme) {
      loadYears();
    }
  }
  
  function handleYearChange(value: string) {
    setSelectedYear(value);
    setModeles([]);
    loadModeles();
  }

  return (
    <div className="container mx-auto py-6">
      <h1 className="text-3xl font-bold mb-6">Gestion des Modèles</h1>
      
      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md mb-6">
          {error}
        </div>
      )}
      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <Card>
          <CardHeader>
            <CardTitle className="text-lg flex items-center gap-2">
              <Database className="w-5 h-5" /> 
              Catégories
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="space-y-2">
                <Label>Famille</Label>
                <Select value={selectedFamily?.toString()} onValueChange={handleFamilyChange}>
                  <SelectTrigger>
                    <SelectValue placeholder="Sélectionner une famille" />
                  </SelectTrigger>
                  <SelectContent>
                    {families.map((family) => (
                      <SelectItem key={family.id} value={family.id.toString()}>
                        {family.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              
              <div className="space-y-2">
                <Label>Gamme</Label>
                <Select 
                  value={selectedGamme?.toString()} 
                  onValueChange={handleGammeChange}
                  disabled={!selectedFamily}
                >
                  <SelectTrigger>
                    <SelectValue placeholder="Sélectionner une gamme" />
                  </SelectTrigger>
                  <SelectContent>
                    {gammes.map((gamme) => (
                      <SelectItem key={gamme.id} value={gamme.id.toString()}>
                        {gamme.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader>
            <CardTitle className="text-lg flex items-center gap-2">
              <Car className="w-5 h-5" /> 
              Véhicule
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="space-y-2">
                <Label>Marque</Label>
                <Select value={selectedMarque?.toString()} onValueChange={handleMarqueChange}>
                  <SelectTrigger>
                    <SelectValue placeholder="Sélectionner une marque" />
                  </SelectTrigger>
                  <SelectContent>
                    {marques.map((marque) => (
                      <SelectItem key={marque.id} value={marque.id.toString()}>
                        {marque.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              
              <div className="space-y-2">
                <Label>Année</Label>
                <Select 
                  value={selectedYear?.toString()} 
                  onValueChange={handleYearChange}
                  disabled={!selectedGamme || !selectedMarque || years.length === 0}
                >
                  <SelectTrigger>
                    <SelectValue placeholder="Sélectionner une année" />
                  </SelectTrigger>
                  <SelectContent>
                    {years.map((year) => (
                      <SelectItem 
                        key={year.value} 
                        value={year.value.toString()}
                        className={year.favorite ? "font-bold" : ""}
                      >
                        {year.label} {year.favorite && "★"}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
      
      <Card className="mb-6">
        <CardHeader>
          <CardTitle className="text-lg flex items-center gap-2">
            <Calendar className="w-5 h-5" /> 
            Modèles Disponibles
          </CardTitle>
        </CardHeader>
        <CardContent>
          {loading ? (
            <div className="text-center py-8">
              <div className="inline-block animate-spin w-8 h-8 border-4 border-gray-300 border-t-blue-600 rounded-full"></div>
              <p className="mt-2 text-gray-500">Chargement en cours...</p>
            </div>
          ) : modeles.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b">
                    <th className="text-left p-2">ID</th>
                    <th className="text-left p-2">Nom</th>
                    <th className="text-left p-2">Années</th>
                  </tr>
                </thead>
                <tbody className="divide-y">
                  {modeles.map((modele) => (
                    <tr key={modele.id} className="hover:bg-gray-50">
                      <td className="p-2">{modele.id}</td>
                      <td className="p-2">{modele.name}</td>
                      <td className="p-2">
                        {modele.yearFrom} - {modele.yearTo}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : selectedYear ? (
            <div className="flex items-center justify-center py-10 text-gray-500">
              <AlertCircle className="w-5 h-5 mr-2" />
              <p>Aucun modèle trouvé pour cette sélection</p>
            </div>
          ) : (
            <div className="text-center py-8 text-gray-500">
              <p>Sélectionnez une gamme, une marque et une année pour voir les modèles disponibles</p>
            </div>
          )}
        </CardContent>
      </Card>
      
      <div className="flex justify-end">
        <Button
          disabled={modeles.length === 0}
          onClick={() => window.print()}
        >
          Exporter la liste
        </Button>
      </div>
    </div>
  );
}
