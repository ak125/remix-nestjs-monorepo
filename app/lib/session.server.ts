import { createCookieSessionStorage, redirect } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import * as bcrypt from "bcryptjs";
import * as crypto from "crypto";

// Types
export interface UserSession {
  id: number;
  username: string;
  role: string;
}

// Stockage de session basé sur les cookies
const sessionStorage = createCookieSessionStorage({
  cookie: {
    name: "__app_session",
    httpOnly: true,
    path: "/",
    sameSite: "lax",
    secrets: [process.env.SESSION_SECRET || "s3cr3t-k3y-ch4ng3-me"],
    secure: process.env.NODE_ENV === "production",
    maxAge: 60 * 60 * 24 * 7, // 7 jours
  },
});

// Obtenir la session à partir de la requête
export async function getSession(request: Request) {
  const cookie = request.headers.get("Cookie");
  return sessionStorage.getSession(cookie);
}

// Obtenir l'utilisateur à partir de la session
export async function getCurrentUser(request: Request): Promise<UserSession | null> {
  const session = await getSession(request);
  const userId = session.get("userId");
  
  if (!userId) {
    return null;
  }
  
  const userSessionId = session.get("sessionId");
  
  if (!userSessionId) {
    return null;
  }
  
  // Vérifier que la session existe toujours en base
  const appSession = await prisma.appSession.findUnique({
    where: {
      id: userSessionId,
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
  
  // Si la session n'existe pas ou a expiré
  if (!appSession || new Date() > appSession.expiresAt || !appSession.user.isActive) {
    await destroySession(request);
    return null;
  }
  
  // Renvoyer les informations utilisateur
  return {
    id: appSession.user.id,
    username: appSession.user.username,
    role: appSession.user.role,
  };
}

// Créer une nouvelle session
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
  // Générer un token unique pour cette session
  const sessionToken = crypto.randomBytes(32).toString("hex");
  
  // Calculer la date d'expiration (7 jours)
  const expiresAt = new Date();
  expiresAt.setDate(expiresAt.getDate() + 7);
  
  // Récupérer des informations sur le client
  const userAgent = request.headers.get("User-Agent");
  const ip = request.headers.get("X-Forwarded-For") || "unknown";
  
  // Créer la session en base de données
  const session = await prisma.appSession.create({
    data: {
      userId,
      token: sessionToken,
      expiresAt,
      userAgent,
      ipAddress: ip,
    },
  });
  
  // Mettre à jour la date de dernière connexion
  await prisma.appUser.update({
    where: { id: userId },
    data: { lastLogin: new Date() },
  });
  
  // Créer la session cookie
  const appSession = await getSession(request);
  appSession.set("userId", userId);
  appSession.set("username", username);
  appSession.set("role", role);
  appSession.set("sessionId", session.id);
  
  return redirect(redirectTo, {
    headers: {
      "Set-Cookie": await sessionStorage.commitSession(appSession),
    },
  });
}

// Authentifier un utilisateur
export async function authenticateUser(username: string, password: string) {
  // Rechercher l'utilisateur par son nom d'utilisateur
  const user = await prisma.appUser.findUnique({ where: { username } });
  
  // Si l'utilisateur n'existe pas ou n'est pas actif
  if (!user || !user.isActive) {
    return null;
  }
  
  // Vérifier le mot de passe
  const isValidPassword = await bcrypt.compare(password, user.password);
  
  // Si le mot de passe est incorrect
  if (!isValidPassword) {
    return null;
  }
  
  // Retourner les informations de l'utilisateur
  return {
    id: user.id,
    username: user.username,
    role: user.role,
  };
}

// Détruire la session
export async function destroySession(request: Request) {
  const session = await getSession(request);
  const sessionId = session.get("sessionId");
  
  // Si un ID de session existe, supprimer la session de la base de données
  if (sessionId) {
    await prisma.appSession.delete({
      where: { id: sessionId },
    }).catch(() => {
      // Ignorer les erreurs si la session n'existe plus
    });
  }
  
  // Détruire la session cookie
  return redirect("/login", {
    headers: {
      "Set-Cookie": await sessionStorage.destroySession(session),
    },
  });
}

// Vérifier l'autorisation pour un rôle spécifique
export async function requireUserRole(request: Request, roles: string[] = []) {
  const user = await getCurrentUser(request);
  
  if (!user) {
    const searchParams = new URLSearchParams([["redirectTo", new URL(request.url).pathname]]);
    throw redirect(`/login?${searchParams}`);
  }
  
  if (roles.length > 0 && !roles.includes(user.role)) {
    throw redirect("/unauthorized");
  }
  
  return user;
}
