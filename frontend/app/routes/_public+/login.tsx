import { Button } from '~/components/ui/button';
import { Input } from '~/components/ui/input';
import { useOptionalUser } from '~/root'; // Assure-toi que le hook est bien importé

export default function Login() {
  const user = useOptionalUser(); // Assure-toi que ce hook est défini correctement
  
  return (
    <div className='max-w-[600px] mx-auto'>
      <h1>Connexion</h1>

      {/* Affiche l'objet user sous forme JSON avec une mise en forme */}
      <pre>{JSON.stringify(user, null, 2)}</pre>

      <form method="post" action="/auth/login" className="flex flex-col gap-2">
        <Input type="email" name="email" placeholder="Email" required /> {/* Utilisation du composant Input */}
        <Input type="password" name="password" placeholder="Mot de passe" required />
        <Button className='ml-auto' type='submit'>Connexion</Button> {/* Utilisation du composant Button */}
      </form>
    </div>
  );
}
