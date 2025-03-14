import { z } from 'zod';

export const ResetPasswordSchema = z.object({
  token: z.string().min(1, 'Token requis'),
  password: z.string()
    .min(8, 'Mot de passe trop court')
    .regex(/[A-Z]/, 'Doit contenir une majuscule')
    .regex(/[0-9]/, 'Doit contenir un chiffre'),
  confirmPassword: z.string(),
}).refine(data => data.password === data.confirmPassword, {
  message: 'Les mots de passe ne correspondent pas',
  path: ['confirmPassword'],
});

export const ChangePasswordSchema = z.object({
  currentPassword: z.string().min(1, 'Mot de passe actuel requis'),
  newPassword: z.string()
    .min(8, 'Mot de passe trop court')
    .regex(/[A-Z]/, 'Doit contenir une majuscule')
    .regex(/[0-9]/, 'Doit contenir un chiffre'),
  confirmPassword: z.string(),
}).refine(data => data.newPassword === data.confirmPassword, {
  message: 'Les mots de passe ne correspondent pas',
  path: ['confirmPassword'],
});

export const AddressSchema = z.object({
  street: z.string().min(1, 'Rue requise'),
  city: z.string().min(1, 'Ville requise'),
  zipCode: z.string().regex(/^\d{5}$/, 'Code postal invalide'),
  country: z.string().min(1, 'Pays requis'),
});

export const BillingAddressSchema = AddressSchema.extend({
  company: z.string().optional(),
  vatNumber: z.string().optional(),
  siret: z.string().regex(/^\d{14}$/, 'SIRET invalide').optional(),
});

// Types inférés pour TypeScript
export type ResetPassword = z.infer<typeof ResetPasswordSchema>;
export type ChangePassword = z.infer<typeof ChangePasswordSchema>;
export type Address = z.infer<typeof AddressSchema>;
export type BillingAddress = z.infer<typeof BillingAddressSchema>;
