import { z } from "zod";

export const userSchema = z.object({
  name: z.string().min(3, "Le nom doit contenir au moins 3 caractères"),
  email: z.string().email("Email invalide"),
  imageUrl: z.string().url("URL invalide").optional().nullable(),
  language: z.enum(["fr", "en", "es"]).default("fr"),
  preferences: z.record(z.unknown()).default({})
});

export type UserFormData = z.infer<typeof userSchema>;

export const formatZodError = (error: z.ZodError) => {
  return Object.fromEntries(
    error.errors.map(err => [
      err.path.join('.'),
      err.message
    ])
  );
};
