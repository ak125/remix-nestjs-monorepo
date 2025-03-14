import { json, ActionFunction } from '@remix-run/node';
import { Form, useActionData } from '@remix-run/react';
import { supabase } from '~/utils/supabase.server';
import { createUserSession } from '~/utils/auth.server';
import { Button } from '~/components/ui/button';
import { Input } from '~/components/ui/input';
import { Card, CardHeader, CardContent, CardFooter } from '~/components/ui/card';

export const action: ActionFunction = async ({ request }) => {
  const form = await request.formData();
  const email = form.get('email') as string;
  const password = form.get('password') as string;

  const { data, error } = await supabase.auth.signInWithPassword({
    email,
    password
  });

  if (error) {
    return json({ error: error.message }, { status: 400 });
  }

  return createUserSession(data.session.access_token, '/dashboard');
};

export default function Login() {
  const actionData = useActionData<typeof action>();

  return (
    <div className="container mx-auto max-w-md p-4">
      <Card>
        <CardHeader>
          <h1 className="text-2xl font-bold">Connexion</h1>
        </CardHeader>
        <CardContent>
          <Form method="post" className="space-y-4">
            <div>
              <Input 
                type="email" 
                name="email" 
                placeholder="Email"
                required 
              />
            </div>
            <div>
              <Input 
                type="password" 
                name="password" 
                placeholder="Mot de passe"
                required 
              />
            </div>
            {actionData?.error && (
              <p className="text-red-500">{actionData.error}</p>
            )}
          </Form>
        </CardContent>
        <CardFooter>
          <Button type="submit" className="w-full">
            Se connecter
          </Button>
        </CardFooter>
      </Card>
    </div>
  );
}
