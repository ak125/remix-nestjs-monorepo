import { json, ActionFunctionArgs } from "@remix-run/node";
import { Form } from "@remix-run/react";
import { Button } from "~/components/ui/button";

export async function action({ request }: ActionFunctionArgs) {
  const form = await request.formData();
  const login = form.get("login");
  const keylog = form.get("keylog");

  const response = await fetch(`${process.env.API_URL}/auth/validate`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ login, keylog })
  });

  const data = await response.json();

  if (!data.accessRequest) {
    return json({ error: data.destinationLinkMsg });
  }

  return json({ success: true });
}

export default function AuthPage() {
  return (
    <div className="container mx-auto py-8 px-4">
      <Form method="post" className="max-w-md mx-auto">
        <div className="space-y-4">
          <input
            type="text"
            name="login"
            placeholder="Login"
            className="w-full px-4 py-2 border rounded"
          />

          <input
            type="password"
            name="keylog" 
            placeholder="Password"
            className="w-full px-4 py-2 border rounded"
          />

          <Button type="submit" className="w-full">
            Se connecter
          </Button>
        </div>
      </Form>
    </div>
  );
}
