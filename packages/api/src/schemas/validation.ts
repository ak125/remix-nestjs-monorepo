import { z } from 'zod';

export const LoginSchema = z.object({
  email: z.string().email('Email invalide'),
  password: z.string().min(6, 'Mot de passe trop court')
});

export const UserSchema = z.object({
  name: z.string().min(2, 'Nom trop court'),
  email: z.string().email('Email invalide'),
  password: z.string().min(6, 'Mot de passe trop court'),
  role: z.enum(['user', 'admin']).default('user')
});

export type LoginInput = z.infer<typeof LoginSchema>;
export type UserInput = z.infer<typeof UserSchema>;
