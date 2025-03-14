import { createClient } from '@supabase/supabase-js';
import { createCookie } from '@remix-run/node';

const supabaseUrl = process.env.SUPABASE_URL!;
const supabaseAnonKey = process.env.SUPABASE_ANON_KEY!;

export const supabase = createClient(supabaseUrl, supabaseAnonKey);

export const sessionCookie = createCookie('sb-session', {
  maxAge: 604_800, // one week
  sameSite: 'lax',
  path: '/',
  httpOnly: true,
  secrets: [process.env.SESSION_SECRET || 'default-secret'],
  secure: process.env.NODE_ENV === 'production'
});
