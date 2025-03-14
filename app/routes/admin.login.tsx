import { json, redirect } from "@remix-run/node";
import { Form, useActionData, useSearchParams } from "@remix-run/react";
import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { Label } from "~/components/ui/label";
import { Alert, AlertDescription } from "~/components/ui/alert";
import { prisma } from "~/lib/db.server";
import { getAdminFromSession, createAdminSession } from "~/lib/admin-session.server";
import * as crypto from "crypto";

export function meta() {
  return [{ title: "Administration - Connexion" }];
}

export async function loader({ request }) {
  // Redirect if already logged in
  const admin = await getAdminFromSession(request);
  if (admin) {
    return redirect("/admin/dashboard");
  }
  
  return json({});
}

export async function action({ request }) {
  const form = await request.formData();
  const login = form.get("login")?.toString();
  const password = form.get("password")?.toString();
  const redirectTo = form.get("redirectTo")?.toString() || "/admin/dashboard";
  
  if (!login || !password) {
    return json(
      { error: "Veuillez fournir un identifiant et un mot de passe" },
      { status: 400 }
    );
  }
  
  // Get the admin account
  const admin = await prisma.adminUser.findUnique({
    where: { login },
  });
  
  if (!admin || !admin.isActive) {
    return json(
      { error: "Identifiants incorrects ou compte désactivé" },
      { status: 401 }
    );
  }
  
  // For backwards compatibility with old md5 passwords
  const md5Password = crypto.createHash("md5").update(password).digest("hex");
  
  // Check if password matches (either bcrypt or legacy md5)
  const passwordMatches = admin.password === md5Password;
  
  if (!passwordMatches) {
    return json({ error: "Identifiants incorrects" }, { status: 401 });
  }
  
  // Update last login time
  await prisma.adminUser.update({
    where: { id: admin.id },
    data: { lastLogin: new Date() },
  });
  
  // Create admin session
  return createAdminSession(admin.id, redirectTo);
}

export default function AdminLoginPage() {
  const actionData = useActionData<typeof action>();
  const [searchParams] = useSearchParams();
  const redirectTo = searchParams.get("redirectTo") || "/admin/dashboard";
  
  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100">
      <div className="w-full max-w-md">
        <div className="bg-white shadow-lg rounded-lg p-8">
          <div className="text-center mb-8">
            <h1 className="text-2xl font-bold">Administration</h1>
            <p className="text-gray-600">Connexion au panneau d'administration</p>
          </div>
          
          {actionData?.error && (
            <Alert variant="destructive" className="mb-6">
              <AlertDescription>{actionData.error}</AlertDescription>
            </Alert>
          )}
          
          <Form method="post" className="space-y-6">
            <input type="hidden" name="redirectTo" value={redirectTo} />
            
            <div className="space-y-2">
              <Label htmlFor="login">Identifiant</Label>
              <Input 
                id="login"
                name="login"
                type="text" 
                required
                autoComplete="username"
              />
            </div>
            
            <div className="space-y-2">
              <Label htmlFor="password">Mot de passe</Label>
              <Input 
                id="password"
                name="password"
                type="password" 
                required
                autoComplete="current-password"
              />
            </div>
            
            <Button type="submit" className="w-full">
              Se connecter
            </Button>
          </Form>
        </div>
      </div>
    </div>
  );
}
