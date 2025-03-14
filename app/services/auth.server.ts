import { createServerClient } from '@supabase/auth-helpers-remix';
import { createCookie } from '@remix-run/node';
import { prisma } from '~/lib/db.server';
import { createCookieSessionStorage, redirect } from "@remix-run/node";
import * as bcrypt from "bcryptjs";
import * as crypto from "crypto";

// Types
interface UserSession {
  id: number;
  username: string;
  role: string;
}

// Configuration du stockage de session basé sur les cookies
const sessionStorage = createCookieSessionStorage({
  cookie: {
    name: "__auth_session",
    httpOnly: true,
    path: "/",
    sameSite: "lax",
    secrets: [process.env.SESSION_SECRET || "s3cr3t-change-this-in-production"],
    secure: process.env.NODE_ENV === "production",
    maxAge: 60 * 60 * 24 * 30, // 30 jours
  },
});

// Récupérer la session depuis la requête
async function getSession(request: Request) {
  const cookie = request.headers.get("Cookie");
  return sessionStorage.getSession(cookie);
}

// Authentifier un utilisateur
export async function authenticateUser(username: string, password: string) {
  // Récupérer l'utilisateur de la base de données
  const user = await prisma.appUser.findUnique({ 
    where: { username },
    select: {
      id: true,
      username: true,
      password: true,
      role: true,
      isActive: true
    }
  });
  
  // Vérifications de l'utilisateur
  if (!user || !user.isActive) {
    return null;
  }
  
  // Vérifier le mot de passe
  const isPasswordValid = await bcrypt.compare(password, user.password);
  if (!isPasswordValid) {
    return null;
  }
  
  // Retourner les informations utilisateur (sans le mot de passe)
  return {
    id: user.id,
    username: user.username,
    role: user.role,
  };
}

// Créer une session utilisateur
export async function createUserSession({
  userId,
  username,
  role,
  request,
  redirectTo,
}: {
  userId: number;
  username: string;
  role: string;
  request: Request;
  redirectTo: string;
}) {
  // Créer un token de session unique
  const sessionToken = crypto.randomBytes(32).toString("hex");
  
  // Date d'expiration (30 jours)
  const expiresAt = new Date();
  expiresAt.setDate(expiresAt.getDate() + 30);
  
  // Récupérer les infos du client
  const userAgent = request.headers.get("User-Agent") || "unknown";
  const ipAddress = request.headers.get("X-Forwarded-For") || "unknown";
  
  // Enregistrer la session dans la base de données
  const session = await prisma.appSession.create({
    data: {
      userId,
      token: sessionToken,
      expiresAt,
      userAgent,
      ipAddress,
    }
  });
  
  // Mettre à jour la date de dernière connexion
  await prisma.appUser.update({
    where: { id: userId },
    data: { lastLogin: new Date() }
  });
  
  // Créer la session cookie
  const cookieSession = await getSession(request);
  cookieSession.set("userId", userId);
  cookieSession.set("username", username);
  cookieSession.set("role", role);
  cookieSession.set("sessionId", session.id);
  
  // Rediriger avec la session dans les cookies
  return redirect(redirectTo, {
    headers: {
      "Set-Cookie": await sessionStorage.commitSession(cookieSession),
    },
  });
}

// Récupérer l'utilisateur actuel depuis la session
export async function getCurrentUser(request: Request): Promise<UserSession | null> {
  const session = await getSession(request);
  const userId = session.get("userId");
  const sessionId = session.get("sessionId");
  
  if (!userId || !sessionId) {
    return null;
  }
  
  // Vérifier que la session existe toujours en base de données
  const appSession = await prisma.appSession.findUnique({
    where: {
      id: sessionId,
      userId: parseInt(userId),
    },
    include: {
      user: {
        select: {
          id: true,
          username: true,
          role: true,
          isActive: true,
        },
      },
    },
  });
  
  // Vérifier la validité de la session
  if (
    !appSession || 
    new Date() > appSession.expiresAt || 
    !appSession.user.isActive
  ) {
    await destroySession(request);
    return null;
  }
  
  return {
    id: appSession.user.id,
    username: appSession.user.username,
    role: appSession.user.role,
  };
}

// Détruire la session utilisateur
export async function destroySession(request: Request) {
  const session = await getSession(request);
  const sessionId = session.get("sessionId");
  
  // Supprimer la session de la base de données
  if (sessionId) {
    await prisma.appSession.delete({
      where: { id: sessionId },
    }).catch(() => {
      // Ignorer les erreurs si la session n'existe pas
    });
  }
  
  // Supprimer les cookies de session
  return redirect("/login", {
    headers: {
      "Set-Cookie": await sessionStorage.destroySession(session),
    },
  });
}

// Vérifier les autorisations de l'utilisateur
export async function requireUserRole(
  request: Request, 
  roles: string[] = [],
  redirectTo = "/login"
) {
  const user = await getCurrentUser(request);
  
  if (!user) {
    const searchParams = new URLSearchParams();
    searchParams.set("redirectTo", new URL(request.url).pathname);
    throw redirect(`${redirectTo}?${searchParams}`);
  }
  
  if (roles.length > 0 && !roles.includes(user.role)) {
    throw redirect("/unauthorized");
  }
  
  return user;
}

// Supprimer automatiquement les sessions expirées
export async function cleanupExpiredSessions() {
  try {
    const result = await prisma.appSession.deleteMany({
      where: {
        expiresAt: {
          lt: new Date(),
        },
      },
    });
    
    return { deletedCount: result.count };
  } catch (error) {
    console.error("Failed to cleanup expired sessions:", error);
    return { error, deletedCount: 0 };
  }
}

const authCookie = createCookie('sb-auth', {
  httpOnly: true,
  secure: process.env.NODE_ENV === 'production',
  sameSite: 'lax',
  maxAge: 604_800
});

export class AuthService {
  async createSupabaseClient(request: Request) {
    const response = new Response();
    
    const supabase = createServerClient(
      process.env.SUPABASE_URL!,
      process.env.SUPABASE_ANON_KEY!,
      { request, response }
    );

    return { supabase, response };
  }

  async getUserRole(userId: string) {
    const user = await prisma.user.findUnique({
      where: { id: userId },
      select: { role: true }
    });

    return user?.role;
  }

  async checkPermission(userId: string, requiredRole: 'ADMIN' | 'SUPER_ADMIN') {
    const role = await this.getUserRole(userId);
    
    if (!role) return false;
    if (requiredRole === 'SUPER_ADMIN' && role !== 'SUPER_ADMIN') return false;
    if (requiredRole === 'ADMIN' && !['ADMIN', 'SUPER_ADMIN'].includes(role)) return false;
    
    return true;
  }
}
