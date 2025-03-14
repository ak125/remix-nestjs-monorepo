import { useMemo } from 'react';
import { createClient } from '@supabase/supabase-js';

const supabaseUrl = process.env.SUPABASE_URL!;
const supabaseKey = process.env.SUPABASE_ANON_KEY!;

export function useSupabase() {
  return useMemo(() => 
    createClient(supabaseUrl, supabaseKey), 
    []
  );
}
