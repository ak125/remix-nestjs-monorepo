import { Button } from "~/components/ui/button";
import { Input } from "~/components/ui/input";
import { useOptionalUser } from "~/root";

export default function Login() {
    const user = useOptionalUser ();
    return (
    <div className="max-w-[600px] mx-auto">
    <h1>Connexion</h1>
    {JSON.stringify(user, null, 2)}
    <form method="post" action="/auth/login" className="space flex flex-col gap-2cd ">
        <label htmlFor="email">Email</label>
        <Input type="email" id="email" name="email" />
        <label htmlFor="password">Mot de passe</label>
        <Input type="password" id="password" name="password" />
        <Button type="submit">Se connecter</Button>
    </form>
    </div>
    );
}