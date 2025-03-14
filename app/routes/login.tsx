import { json, redirect } from "@remix-run/node";
import { Form, useActionData, useSearchParams, Link } from "@remix-run/react";
import { authenticateUser, createUserSession, getCurrentUser } from "~/services/session.server";
import { useState } from "react";
import { Eye, EyeOff, Lock, Mail } from "lucide-react";
import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { Label } from "~/components/ui/label";
import { Alert, AlertDescription } from "~/components/ui/alert";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "~/components/ui/card";

export async function loader({ request }) {
  // Vérifier si l'utilisateur est déjà connecté
  const user = await getCurrentUser(request);
  if (user) {
    return redirect("/dashboard");
  }
  return json({});
}

export async function action({ request }) {
  const formData = await request.formData();
  const email = formData.get("email");
  const password = formData.get("password");
  const redirectTo = formData.get("redirectTo") || "/dashboard";
  
  // Valider les entrées
  if (
    typeof email !== "string" || 
    typeof password !== "string" ||
    email.length === 0 ||
    password.length === 0
  ) {
    return json(
      { error: "Email et mot de passe sont requis" },
      { status: 400 }
    );
  }
  
  // Authentifier l'utilisateur
  const user = await authenticateUser(email, password);
  
  if (!user) {
    return json(
      { error: "Identifiants incorrects ou compte inactif" },
      { status: 401 }
    );
  }
  
  // Créer la session et rediriger
  return createUserSession({
    userId: user.id,
    email: user.email,
    role: user.role,
    request,
    redirectTo: redirectTo.toString(),
  });
}

export default function LoginPage() {
  const actionData = useActionData<typeof action>();
  const [searchParams] = useSearchParams();
  const redirectTo = searchParams.get("redirectTo") || "/dashboard";
  
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  
  return (
    <div className="min-h-screen flex flex-col justify-center items-center bg-gray-50 p-4">
      <Card className="w-full max-w-md">
        <CardHeader className="space-y-1">
          <CardTitle className="text-2xl font-bold text-center">Connexion</CardTitle>
          <CardDescription className="text-center">
            Entrez vos identifiants pour accéder à votre compte
          </CardDescription>
        </CardHeader>
        
        <CardContent>
          {actionData?.error && (
            <Alert variant="destructive" className="mb-6">
              <AlertDescription>{actionData.error}</AlertDescription>
            </Alert>
          )}
          
          <Form 
            method="post" 
            className="space-y-4"
            onSubmit={() => setIsSubmitting(true)}
          >
            <input type="hidden" name="redirectTo" value={redirectTo} />
            
            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <div className="relative">
                <div className="absolute left-3 top-3 text-gray-400">
                  <Mail size={16} />
                </div>
                <Input 
                  id="email"
                  name="email"
                  type="email"
                  autoComplete="email"
                  className="pl-10"
                  required
                />
              </div>
            </div>
            
            <div className="space-y-2">
              <div className="flex justify-between items-center">
                <Label htmlFor="password">Mot de passe</Label>
                <Link 
                  to="/forgot-password"
                  className="text-sm text-primary hover:underline"
                >
                  Mot de passe oublié ?
                </Link>
              </div>
              <div className="relative">
                <div className="absolute left-3 top-3 text-gray-400">
                  <Lock size={16} />
                </div>
                <Input 
                  id="password"
                  name="password"
                  type={showPassword ? "text" : "password"}
                  autoComplete="current-password"
                  className="pl-10 pr-10"
                  required
                />
                <Button 
                  type="button"
                  variant="ghost"
                  size="sm"
                  className="absolute right-1 top-1 h-8 w-8 p-0"
                  onClick={() => setShowPassword(!showPassword)}
                >
                  {showPassword ? (
                    <EyeOff className="h-4 w-4 text-gray-500" />
                  ) : (
                    <Eye className="h-4 w-4 text-gray-500" />
                  )}
                  <span className="sr-only">
                    {showPassword ? "Masquer le mot de passe" : "Afficher le mot de passe"}
                  </span>
                </Button>
              </div>
            </div>
            
            <Button 
              type="submit"
              className="w-full"
              disabled={isSubmitting}
            >
              {isSubmitting ? "Connexion en cours..." : "Se connecter"}
            </Button>
          </Form>
        </CardContent>
        
        <CardFooter className="flex flex-col">
          <p className="text-center text-sm text-gray-500">
            Cette application nécessite une authentification.
          </p>
        </CardFooter>
      </Card>
    </div>
  );
}
