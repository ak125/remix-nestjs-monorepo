import { z } from 'zod';

export const UpdateBillingAddressDto = z.object({
  civ: z.string().optional(),
  nom: z.string().min(1, "Le nom est obligatoire"),
  prenom: z.string().optional(),
  tel: z.string().optional(),
  gsm: z.string().min(1, "Le numéro de GSM est obligatoire").regex(/^\d+$/, "Le GSM doit contenir uniquement des chiffres"),
  adr: z.string().min(1, "L'adresse est obligatoire"),
  zipcode: z.string().min(1, "Le code postal est obligatoire").regex(/^\d+$/, "Le code postal doit contenir uniquement des chiffres"),
  ville: z.string().min(1, "La ville est obligatoire"),
  pays: z.string().min(1, "Le pays est obligatoire"),
});

export type UpdateBillingAddressDtoType = z.infer<typeof UpdateBillingAddressDto>;
