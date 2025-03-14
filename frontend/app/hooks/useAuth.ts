import { useState, useCallback } from 'react';
import { authApi } from '../services/api.service';

interface User {
  id: string;
  email: string;
  prenom: string;
}

export function useAuth() {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const login = useCallback(async (email: string, password: string) => {
    try {
      setLoading(true);
      setError(null);
      const response = await authApi.login(email, password);
      setUser(response.user);
      return response;
    } catch (err) {
      setError('Échec de la connexion');
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  const logout = useCallback(async () => {
    try {
      await authApi.logout();
      setUser(null);
    } catch (err) {
      setError('Échec de la déconnexion');
      throw err;
    }
  }, []);

  const checkAccess = useCallback(async () => {
    try {
      setLoading(true);
      const html = await authApi.checkAccessHtml();
      return html;
    } catch (err) {
      setError('Accès non autorisé');
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  return {
    user,
    loading,
    error,
    login,
    logout,
    checkAccess,
  };
}
