import { z } from 'zod';

export const ChangePasswordDto = z.object({
  oldPassword: z.string().min(6, "L'ancien mot de passe doit contenir au moins 6 caractères."),
  newPassword: z.string().min(8, "Le nouveau mot de passe doit contenir au moins 8 caractères."),
});
