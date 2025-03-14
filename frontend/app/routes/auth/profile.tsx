import { json, LoaderFunction, ActionFunction, redirect } from "@remix-run/node";
import { useLoaderData, useFetcher } from "@remix-run/react";
import { useEffect, useState } from "react";
import { getUserSession } from "~/server/auth.server";
import { updateUserAddress } from "~/server/user.server";
import { AccountMenu } from "~/components/AccountMenu";

/**
 * ✅ Vérification de l'utilisateur via session
 */
export const loader: LoaderFunction = async ({ request }) => {
  const user = await getUserSession(request);
  if (!user) {
    return redirect("/login");
  }
  return json({ user });
};

/**
 * ✅ Mise à jour de l'adresse utilisateur
 */
export const action: ActionFunction = async ({ request }) => {
  const formData = await request.formData();
  const addressData = Object.fromEntries(formData);

  const result = await updateUserAddress(request, addressData);

  if (!result.success) {
    return json({ error: result.error }, { status: 400 });
  }

  return json({ message: "Adresse mise à jour avec succès !" });
};

/**
 * ✅ Composant ProfilePage
 */
export default function ProfilePage() {
  const { user } = useLoaderData<typeof loader>();
  const fetcher = useFetcher();
  const [message, setMessage] = useState("");

  useEffect(() => {
    if (fetcher.data?.message) {
      setMessage(fetcher.data.message);
    }
  }, [fetcher.data]);

  return (
    <div className="container mx-auto p-6">
      <h1 className="text-2xl font-bold mb-6">Mon compte</h1>

      {/* ✅ Menu du compte */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <AccountMenu
          title="Tableau de Bord"
          items={[
            { label: "Tableau de bord", path: "/dashboard" },
            { label: "Mes commandes", path: "/orders" },
          ]}
        />

        <AccountMenu
          title="Paramètres"
          items={[
            { label: "Changer mon mot de passe", path: "/password" },
            { label: "Déconnexion", path: "/logout" },
          ]}
        />

        <AccountMenu
          title="Automécanik"
          items={[
            { label: "À propos", path: "/about" },
            { label: "Nos services", path: "/services" },
          ]}
        />

        <AccountMenu
          title="Aide & Support"
          items={[
            { label: "FAQ", path: "/faq" },
            { label: "Contactez-nous", path: "/contact" },
          ]}
        />
      </div>

      {/* ✅ Formulaire de mise à jour d'adresse */}
      <div className="mt-8 p-4 border rounded-md">
        <h2 className="text-xl font-semibold mb-4">Modifier mon adresse</h2>
        {message && <p className="text-green-600">{message}</p>}
        {fetcher.data?.error && <p className="text-red-600">{fetcher.data.error}</p>}

        <fetcher.Form method="post" className="space-y-3">
          <label className="block">
            Nom *
            <input type="text" name="nom" defaultValue={user.nom} required className="input-field" />
          </label>

          <label className="block">
            Prénom
            <input type="text" name="prenom" defaultValue={user.prenom} className="input-field" />
          </label>

          <label className="block">
            Adresse *
            <input type="text" name="adr" defaultValue={user.adr} required className="input-field" />
          </label>

          <label className="block">
            Code postal *
            <input type="text" name="zipcode" defaultValue={user.zipcode} required className="input-field" />
          </label>

          <label className="block">
            Ville *
            <input type="text" name="ville" defaultValue={user.ville} required className="input-field" />
          </label>

          <label className="block">
            Pays *
            <input type="text" name="pays" defaultValue={user.pays} required className="input-field" />
          </label>

          <button type="submit" className="btn-primary">Mettre à jour</button>
        </fetcher.Form>
      </div>
    </div>
  );
}
