import { z } from 'zod';

export const ResetPasswordDto = z.object({
  email: z.string().email({ message: "L'email doit être valide" }),
});

export type ResetPasswordDtoType = z.infer<typeof ResetPasswordDto>;
