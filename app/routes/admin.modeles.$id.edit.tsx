import { useState } from "react";
import { json, redirect, LoaderFunction, ActionFunction } from "@remix-run/node";
import { useLoaderData, useActionData, Form, Link, useNavigate } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { Label } from "~/components/ui/label";
import { Checkbox } from "~/components/ui/checkbox";
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "~/components/ui/card";
import { requireUserRole } from "~/services/auth.server";
import { Alert, AlertDescription } from "~/components/ui/alert";
import { ArrowLeft, Save } from "lucide-react";
import { fetchModeleById, updateModele } from "~/services/modele.server";
import { fetchAllMarques } from "~/services/marque.server";
import { Marque, Modele, UpdateModeleData } from "~/types/modele.types";

interface LoaderData {
  modele: Modele;
  marques: Marque[];
  error?: string;
}

interface ActionData {
  success: boolean;
  message?: string;
}

export const loader: LoaderFunction = async ({ request, params }): Promise<Response> => {
  // Vérifier que l'utilisateur est administrateur
  await requireUserRole(request, ["ADMIN"]);
  
  const { id } = params;
  
  if (!id) {
    return json<LoaderData>({
      error: "ID du modèle manquant",
      modele: {} as Modele,
      marques: []
    });
  }
  
  try {
    // Récupérer les données en parallèle pour optimiser les performances
    const [modele, marques] = await Promise.all([
      fetchModeleById(id),
      fetchAllMarques()
    ]);
    
    return json<LoaderData>({ modele, marques });
  } catch (error) {
    console.error("Error loading data:", error);
    return json<LoaderData>({
      error: error instanceof Error ? error.message : "Impossible de charger les données du modèle",
      modele: {} as Modele,
      marques: []
    });
  }
};

export const action: ActionFunction = async ({ request, params }): Promise<Response> => {
  // Vérifier que l'utilisateur est administrateur
  await requireUserRole(request, ["ADMIN"]);
  
  const { id } = params;
  
  if (!id) {
    return json<ActionData>({
      success: false,
      message: "ID du modèle manquant"
    });
  }
  
  const formData = await request.formData();
  
  // Extraire les données du formulaire
  const name = formData.get("name") as string;
  const alias = formData.get("alias") as string;
  const marqueId = formData.get("marqueId") as string;
  const yearFrom = formData.get("yearFrom") as string;
  const yearToValue = formData.get("yearTo") as string;
  const sort = formData.get("sort") as string;
  const display = formData.has("display");
  
  // Validation des champs obligatoires
  if (!name || !alias || !marqueId || !yearFrom) {
    return json<ActionData>({
      success: false,
      message: "Tous les champs obligatoires doivent être remplis"
    });
  }
  
  // Préparer les données pour la mise à jour
  const updateData: UpdateModeleData = {
    name,
    alias,
    marqueId,
    yearFrom: parseInt(yearFrom),
    yearTo: yearToValue ? parseInt(yearToValue) : null,
    sort: sort ? parseInt(sort) : 0,
    display
  };
  
  try {
    await updateModele(id, updateData);
    
    // Rediriger vers la liste des modèles
    return redirect("/admin/modeles");
  } catch (error) {
    console.error("Error updating model:", error);
    return json<ActionData>({
      success: false,
      message: error instanceof Error ? error.message : "Une erreur est survenue lors de la mise à jour du modèle"
    });
  }
};

export default function EditModele() {
  const { modele, marques, error } = useLoaderData<LoaderData>();
  const actionData = useActionData<ActionData>();
  const navigate = useNavigate();
  
  const [isModified, setIsModified] = useState(false);
  
  // Gérer les changements de formulaire pour activer le bouton de sauvegarde
  const handleChange = () => {
    setIsModified(true);
  };
  
  // Gérer le retour sans sauvegarde
  const handleCancel = () => {
    if (isModified && !window.confirm("Des modifications non sauvegardées seront perdues. Voulez-vous continuer ?")) {
      return;
    }
    navigate("/admin/modeles");
  };
  
  if (!modele.id) {
    return (
      <div className="container py-6">
        <Card>
          <CardContent className="pt-6">
            <Alert variant="destructive">
              <AlertDescription>
                {error || "Modèle non trouvé"}
              </AlertDescription>
            </Alert>
            <div className="flex justify-center mt-6">
              <Button asChild>
                <Link to="/admin/modeles">Retour à la liste</Link>
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    );
  }
  
  return (
    <div className="container py-6">
      <Form method="post" onChange={handleChange}>
        <Card className="mb-6">
          <CardHeader className="flex flex-row items-center justify-between">
            <CardTitle className="text-2xl">Modifier le modèle</CardTitle>
            <Button variant="outline" type="button" onClick={handleCancel}>
              <ArrowLeft className="mr-2 h-4 w-4" /> Retour
            </Button>
          </CardHeader>
          <CardContent className="space-y-6">
            {actionData?.message && !actionData.success && (
              <Alert variant="destructive">
                <AlertDescription>{actionData.message}</AlertDescription>
              </Alert>
            )}
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-2">
                <Label htmlFor="name">Nom *</Label>
                <Input
                  id="name"
                  name="name"
                  defaultValue={modele.name}
                  required
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="alias">Alias *</Label>
                <Input
                  id="alias"
                  name="alias"
                  defaultValue={modele.alias}
                  required
                />
                <p className="text-xs text-gray-500">
                  Utilisé dans les URLs, doit être unique
                </p>
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="marqueId">Marque *</Label>
                <select 
                  id="marqueId" 
                  name="marqueId" 
                  className="w-full p-2 border rounded"
                  defaultValue={modele.marqueId}
                  required
                >
                  <option value="">Sélectionner une marque</option>
                  {marques.map(marque => (
                    <option key={marque.id} value={marque.id}>
                      {marque.name}
                    </option>
                  ))}
                </select>
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="sort">Ordre de tri</Label>
                <Input
                  type="number"
                  id="sort"
                  name="sort"
                  defaultValue={modele.sort || 0}
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="yearFrom">Année de début *</Label>
                <Input
                  type="number"
                  id="yearFrom"
                  name="yearFrom"
                  min="1900"
                  max="2100"
                  defaultValue={modele.yearFrom}
                  required
                />
              </div>
              
              <div className="space-y-2">
                <Label htmlFor="yearTo">Année de fin</Label>
                <Input
                  type="number"
                  id="yearTo"
                  name="yearTo"
                  min="1900"
                  max="2100"
                  defaultValue={modele.yearTo || ""}
                />
                <p className="text-xs text-gray-500">
                  Laissez vide pour les modèles actuels
                </p>
              </div>
              
              <div className="flex items-center space-x-2">
                <Checkbox 
                  id="display" 
                  name="display" 
                  defaultChecked={modele.display} 
                />
                <Label htmlFor="display">Modèle actif</Label>
              </div>
            </div>
          </CardContent>
          <CardFooter className="flex justify-between">
            <Button variant="outline" type="button" onClick={handleCancel}>
              Annuler
            </Button>
            <Button type="submit" disabled={!isModified}>
              <Save className="mr-2 h-4 w-4" /> Enregistrer
            </Button>
          </CardFooter>
        </Card>
      </Form>
    </div>
  );
}
