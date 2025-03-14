import { z } from 'zod';

export const RegisterSchema = z.object({
  email: z.string().email('Email invalide'),
  password: z.string().min(6, 'Le mot de passe doit contenir au moins 6 caractères'),
  passwordConfirm: z.string(),
  civility: z.enum(['Mr.', 'Mme.', 'Mlle.']),
  lastName: z.string().min(1, 'Le nom est requis'),
  firstName: z.string().optional(),
  address: z.string().min(1, 'L\'adresse est requise'),
  zipCode: z.string().regex(/^\d{5}$/, 'Le code postal doit contenir 5 chiffres'),
  city: z.string().min(1, 'La ville est requise'),
  country: z.string().default('France'),
  phone: z.string().optional(),
  mobile: z.string().regex(/^\d{6,}$/, 'Le numéro de GSM doit contenir au moins 6 chiffres'),
}).refine(data => data.password === data.passwordConfirm, {
  message: 'Les mots de passe ne correspondent pas',
  path: ['passwordConfirm'],
});

export type RegisterDto = z.infer<typeof RegisterSchema>;
