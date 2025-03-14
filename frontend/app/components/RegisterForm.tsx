import { useState } from 'react';
import { useAuth } from '../hooks/useAuth';

export function RegisterForm() {
  const { register, loading, error } = useAuth();
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    passwordConfirm: '',
    civility: 'Mr.',
    lastName: '',
    firstName: '',
    address: '',
    zipCode: '',
    city: '',
    country: 'France',
    mobile: '',
  });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    try {
      const response = await register(formData);
      if (response.user) {
        window.location.href = '/dashboard';
      }
    } catch (err) {
      console.error('Erreur d\'inscription:', err);
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    setFormData(prev => ({
      ...prev,
      [e.target.name]: e.target.value
    }));
  };

  return (
    <form onSubmit={handleSubmit} className="register-form">
      {error && <div className="alert alert-danger">{error}</div>}
      
      <h2>Données de connexion</h2>
      <div className="form-group">
        <input
          type="email"
          name="email"
          value={formData.email}
          onChange={handleChange}
          placeholder="Email *"
          required
        />
      </div>
      {/* ...autres champs du formulaire... */}
      
      <button type="submit" disabled={loading}>
        {loading ? 'Création...' : 'Créer mon compte'}
      </button>
    </form>
  );
}
