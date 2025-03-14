import { useMatches } from "@remix-run/react";

interface User {
  id: string;
  role: number;
  firstName?: string;
  lastName?: string;
}

export function useUser() {
  const matches = useMatches();
  const rootData = matches.find(match => match.id === "root")?.data;
  
  // Le loader `root` contient les informations de l'utilisateur 
  // s'il est authentifié
  const user = rootData?.user as User | undefined;
  
  return {
    user,
    isAuthenticated: !!user,
    isAdmin: user?.role >= 6 || false,
    isSuperAdmin: user?.role >= 9 || false
  };
}
