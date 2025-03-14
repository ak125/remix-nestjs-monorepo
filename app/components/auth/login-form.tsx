import { useState } from "react";
import { Form, useActionData } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { Label } from "~/components/ui/label";
import { Alert, AlertDescription } from "~/components/ui/alert";
import { Eye, EyeOff, Lock, User } from "lucide-react";

interface LoginFormProps {
  redirectTo?: string;
  error?: string;
}

export function LoginForm({ redirectTo = "/dashboard", error }: LoginFormProps) {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const actionData = useActionData();
  
  return (
    <div className="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
      <h1 className="text-2xl font-bold mb-6 text-center">Connexion</h1>
      
      {(error || actionData?.error) && (
        <Alert variant="destructive" className="mb-6">
          <AlertDescription>
            {error || actionData?.error}
          </AlertDescription>
        </Alert>
      )}
      
      <Form 
        method="post" 
        className="space-y-6"
        onSubmit={() => setIsSubmitting(true)}
      >
        <input type="hidden" name="redirectTo" value={redirectTo} />
        
        <div className="space-y-2">
          <Label htmlFor="username">Nom d'utilisateur</Label>
          <div className="relative">
            <div className="absolute left-3 top-3 text-gray-400">
              <User size={16} />
            </div>
            <Input 
              id="username"
              name="username"
              type="text"
              autoComplete="username"
              className="pl-10"
              required
            />
          </div>
        </div>
        
        <div className="space-y-2">
          <Label htmlFor="password">Mot de passe</Label>
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
    </div>
  );
}
