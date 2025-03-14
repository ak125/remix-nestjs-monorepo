import { json, redirect } from "@remix-run/node";
import { useLoaderData, Form, Link } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "~/components/ui/card";
import { requireUserRole } from "~/services/auth.server";
import { Alert, AlertDescription } from "~/components/ui/alert";
import { ArrowLeft, Trash2, AlertTriangle } from "lucide-react";

export async function loader({ request, params }) {
  // Vérifier que l'utilisateur est administrateur
  await requireUserRole(request, ["ADMIN"]);
  
  const { id } = params;
  
  try {
    // Récupérer les détails du modèle
    const response = await fetch(
      `${process.env.API_BASE_URL || 'http://localhost:3000'}/api/modele/${id}`
    );
    
    if (!response.ok) {
      throw new Error(`API error: ${response.status}`);
    }
    
    const modele = await response.json();
    
    return json({ modele });
  } catch (error) {
    console.error("Error loading data:", error);
    return json({ 
      error: "Impossible de charger les données du modèle",
      modele: null 
    });
  }
}

export async function action({ request, params }) {
  // Vérifier que l'utilisateur est administrateur
  await requireUserRole(request, ["ADMIN"]);
  
  const { id } = params;
  
  try {
    // Suppression du modèle via l'API
    const response = await fetch(
      `${process.env.API_BASE_URL || 'http://localhost:3000'}/api/modele/${id}`,
      {
        method: "DELETE"
      }
    );
    
    if (!response.ok) {
      const errorData = await response.json();
      return json({
        success: false,
        message: errorData.message || "Erreur lors de la suppression du modèle"
      });
    }
    
    // Rediriger vers la liste des modèles
    return redirect("/admin/modeles");
  } catch (error) {
    console.error("Error deleting model:", error);
    return json({
      success: false,
      message: "Une erreur est survenue lors de la suppression du modèle"
    });
  }
}

export default function DeleteModele() {
  const { modele, error } = useLoaderData<typeof loader>();
  
  if (!modele) {
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
      <Card className="border-red-200">
        <CardHeader>
          <CardTitle className="flex items-center gap-2 text-red-600">
            <AlertTriangle className="h-5 w-5" />
            Confirmation de suppression
          </CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="bg-red-50 p-4 rounded-md border border-red-200">
            <p className="font-medium text-lg">
              Êtes-vous sûr de vouloir supprimer ce modèle ?
            </p>
            <p className="text-gray-500 mt-2">
              Cette action est irréversible et supprimera définitivement :
            </p>
            <ul className="list-disc list-inside mt-2 space-y-1 text-gray-600">
              <li>Le modèle <strong>{modele.name}</strong></li>
              <li>Tous les types (motorisations) associés</li>
              <li>Les relations avec les pièces et gammes</li>
            </ul>
          </div>
          
          <div className="bg-yellow-50 p-4 rounded-md border border-yellow-200">
            <h3 className="font-medium">Détails du modèle :</h3>
            <p><strong>ID :</strong> {modele.id}</p>
            <p><strong>Nom :</strong> {modele.name}</p>
            <p><strong>Marque :</strong> {modele.marque?.name}</p>
            <p><strong>Années :</strong> {modele.yearFrom} - {modele.yearTo || "Présent"}</p>
          </div>
        </CardContent>
        <CardFooter className="flex justify-between">
          <Button variant="outline" asChild>
            <Link to="/admin/modeles">
              <ArrowLeft className="mr-2 h-4 w-4" /> Annuler
            </Link>
          </Button>
          
          <Form method="post">
            <Button variant="destructive" type="submit">
              <Trash2 className="mr-2 h-4 w-4" /> Confirmer la suppression
            </Button>
          </Form>
        