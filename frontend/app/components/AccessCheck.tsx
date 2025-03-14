import { useEffect } from 'react';
import { authApi } from '../services/api.service';

export function AccessCheck() {
  useEffect(() => {
    const checkAccess = async () => {
      try {
        const html = await authApi.checkAccessHtml();
        document.body.innerHTML = html;
      } catch (error) {
        console.error('Erreur lors de la vérification d\'accès:', error);
        window.location.href = '/login';
      }
    };

    checkAccess();
  }, []);

  return <div>Vérification de l'accès...</div>;
}
