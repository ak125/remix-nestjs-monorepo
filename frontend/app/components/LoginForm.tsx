import { useState } from 'react';
import { useLogin } from '../hooks/useLogin';

export function LoginForm() {
  const { login, loading, error } = useLogin();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    try {
      const response = await login(email, password);
      // Redirection après connexion réussie
      if (response.user) {
        window.location.href = '/dashboard';
      }
    } catch (err) {
      console.error('Erreur de connexion:', err);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="login-form">
      {error && <div className="alert alert-danger">{error}</div>}
      <div className="form-group">
        <input
          type="email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          placeholder="Email"
          required
        />
      </div>
      <div className="form-group">
        <input
          type="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          placeholder="Mot de passe"
          required
        />
      </div>
      <button type="submit" disabled={loading}>
        {loading ? 'Connexion...' : 'Se connecter'}
      </button>
    </form>
  );
}
