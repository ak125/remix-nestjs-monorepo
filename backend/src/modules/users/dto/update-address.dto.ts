import { z } from 'zod';

export const UpdateAddressDto = z.object({
  civ: z.string().min(2, "La civilité est requise."),
  nom: z.string().min(2, "Le nom est requis."),
  prenom: z.string().optional(),
  tel: z.string().min(8, "Le téléphone est requis."),
  gsm: z.string().optional(),
  adr: z.string().min(5, "L'adresse est requise."),
  zipcode: z.string().min(4, "Le code postal est requis."),
  ville: z.string().min(2, "La ville est requise."),
  pays: z.string().min(2, "Le pays est requis."),
});
