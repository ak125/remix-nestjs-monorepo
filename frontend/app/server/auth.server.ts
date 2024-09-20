import { redirect, type AppLoadContext } from "@remix-run/node";
import { z } from 'zod';

const authenticatedUserSchema = z.object({
  id: z.string(),
  name: z.string(),
  email: z.string(),
});

export const getOptionalUser = async ({context}: {context: AppLoadContext}) => {
  return authenticatedUserSchema.optional().nullable().parse(context.User);
  if (User) {
    return await context.remixService.getUser({
      userId: user.id
    })
  }
  return null;
}

export const reqireUser = async ({context}: {context: AppLoadContext}) => {
  const user = await getOptionalUser({context});
  if (!user) {
    throw redirect('/login');
  }
  return user;
}