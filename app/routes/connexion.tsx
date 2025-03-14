import { json, redirect } from "@remix-run/node";
import { useSearchParams } from "@remix-run/react";
import { LoginForm } from "~/components/auth/login-form";
import { authenticateUser, createUserSession, getCurrentUser } from "~/services/auth.server";

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
  const username = formData.get("username");
  const password = formData.get("password");
  const redirectTo = formData.get("redirectTo") || "/dashboard";
  
  // Valider les entrées
  if (
    typeof username !== "string" || 
    typeof password !== "string" ||
    username.length === 0 ||
    password.length === 0
  ) {
    return json(
      { error: "Veuillez fournir un nom d'utilisateur et un mot de passe valides" },
      { status: 400 }
    );
  }
  
  // Authentifier l'utilisateur
  const user = await authenticateUser(username, password);
  
  if (!user) {
    return json(
      { error: "Identifiants incorrects ou compte inactif" },
      { status: 401 }
    );
  }
  
  // Créer la session et rediriger
  return createUserSession({
    userId: user.id,
    username: user.username,
    role: user.role,
    request,
    redirectTo: redirectTo.toString(),
  });
}

export default function LoginPage() {
  const [searchParams] = useSearchParams();
  const redirectTo = searchParams.get("redirectTo") || "/dashboard";
  
  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100">
      <div className="px-4 w-full max-w-md">
        <LoginForm redirectTo={redirectTo} />
        
        <div className="mt-6 text-center text-sm text-gray-500">
          <p>
            Besoin d'aide pour vous connecter?{" "}
            <a href="/contact" className="text-blue-600 hover:underline">
              Contactez-nous
            </a>
          </p>
        </div>
      </div>
    </div>
  );
}
