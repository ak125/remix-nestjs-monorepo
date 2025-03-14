import { json } from "@remix-run/node";
import { useLoaderData, useSearchParams, Form } from "@remix-run/react";
import { useState, useEffect } from "react";
import { YearSelector } from "~/components/car/year-selector";
import { ModelSelector } from "~/components/car/model-selector";
import { Button } from "~/components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "~/components/ui/select";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "~/components/ui/card";
import { ArrowRight } from "lucide-react";
import { Label } from "~/components/ui/label";

export async function loader() {
  // Récupérer toutes les marques disponibles
  const response = await fetch(`${process.env.API_BASE_URL || 'http://localhost:3000'}/api/marques`);
  const marques = await response.json();

  // Générer les années pour le sélecteur (de l'année courante jusqu'à 20 ans en arrière)
  const currentYear = new Date().getFullYear();
  const years = Array.from({ length: 20 }, (_, i) => currentYear - i);

  return json({ marques, years });
}

export default function RechercheVehicule() {
  const { marques, years } = useLoaderData<typeof loader>();
  const [searchParams, setSearchParams] = useSearchParams();
  
  // États du formulaire
  const [marqueId, setMarqueId] = useState(searchParams.get("marque") || "");
  const [yearValue, setYearValue] = useState(searchParams.get("year") || "");
  const [modeleId, setModeleId] = useState(searchParams.get("modele") || "");
  
  // ID de gamme fixe pour la démonstration (à adapter selon votre logique métier)
  const gammeId = "fixed-gamme-id";
  
  // Mettre à jour les paramètres d'URL lorsque les sélections changent
  useEffect(() => {
    const params = new URLSearchParams();
    if (marqueId) params.set("marque", marqueId);
    if (yearValue) params.set("year", yearValue);
    if (modeleId) params.set("modele", modeleId);
    setSearchParams(params, { replace: true });
  }, [marqueId, yearValue, modeleId, setSearchParams]);
  
  // Réinitialiser le modèle lorsque la marque ou l'année change
  useEffect(() => {
    setModeleId("");
  }, [marqueId, yearValue]);

  return (
    <div className="container py-10">
      <h1 className="text-3xl font-bold mb-6">Recherche par véhicule</h1>
      
      <Card className="mb-8">
        <CardHeader>
          <CardTitle>Sélectionnez votre véhicule</CardTitle>
          <CardDescription>
            Trouvez les pièces compatibles avec votre voiture
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Form method="get" className="space-y-6" action="/pieces-compatibles">
            {/* Première ligne: Marque et Année */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-2">
                <Label htmlFor="marque-selector">Marque</Label>
                <Select
                  value={marqueId}
                  onValueChange={(value) => setMarqueId(value)}
                >
                  <SelectTrigger id="marque-selector">
                    <SelectValue placeholder="Sélectionner une marque" />
                  </SelectTrigger>
                  <SelectContent>
                    {marques.map((marque) => (
                      <SelectItem key={marque.id} value={marque.id}>
                        {marque.name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              
              <div>
                {marqueId && (
                  <YearSelector
                    gammeId={gammeId}
                    marqueId={marqueId}
                    value={yearValue}
                    onChange={setYearValue}
                    placeholder="Sélectionner une année"
                  />
                )}
              </div>
            </div>
            
            {/* Deuxième ligne: Modèle (chargé dynamiquement) */}
            <div>
              {marqueId && yearValue && (
                <ModelSelector
                  gammeId={gammeId}
                  marqueId={marqueId}
                  year={yearValue}
                  value={modeleId}
                  onChange={setModeleId}
                  placeholder="Sélectionner un modèle"
                />
              )}
            </div>
            
            {/* Bouton de recherche */}
            <div className="flex justify-end">
              <Button 
                type="submit"
                disabled={!modeleId}
                className="flex items-center"
              >
                <span>Rechercher les pièces</span>
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </div>
            
            <input type="hidden" name="marque" value={marqueId} />
            <input type="hidden" name="year" value={yearValue} />
            <input type="hidden" name="modele" value={modeleId} />
          </Form>
        </CardContent>
      </Card>
      
      <div className="text-center text-sm text-gray-500">
        Notre base de données contient plus de 15 000 modèles compatibles avec nos produits.
      </div>
    </div>
  );
}
