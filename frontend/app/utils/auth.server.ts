import { json, redirect } from '@remix-run/node';
import { supabase } from './supabase.server';
import { createCookieSessionStorage } from '@remix-run/node';

const sessionStorage = createCookieSessionStorage({
  cookie: {
    name: 'sb:token',
    httpOnly: true,
    path: '/',
    sameSite: 'lax',
    secrets: [process.env.SESSION_SECRET || 'default-secret'],
    secure: process.env.NODE_ENV === 'production',
  },
});

export async function requireUser(request: Request) {
  const session = await sessionStorage.getSession(
    request.headers.get('Cookie')
  );
  
  const accessToken = session.get('accessToken');
  if (!accessToken) {
    throw redirect('/login');
  }

  const { data: { user }, error } = await supabase.auth.getUser(accessToken);
  if (error || !user) {
    throw redirect('/login');
  }

  return user;
}

export async function createUserSession(accessToken: string, redirectTo: string) {
  const session = await sessionStorage.getSession();
  session.set('accessToken', accessToken);

  return redirect(redirectTo, {
    headers: {
      'Set-Cookie': await sessionStorage.commitSession(session),
    },
  });
}
