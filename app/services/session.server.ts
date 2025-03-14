import { createCookieSessionStorage, redirect } from "@remix-run/node";
import { redisSessionService } from "~/services/redis.server";
import { prisma } from "~/lib/db.server";
import * as crypto from "crypto";
import * as bcrypt from "bcryptjs";

export interface UserSession {
  id: string;
  email: string;
  role: string;
}

// Configuration du cookie de session
const sessionCookieStorage = createCookieSessionStorage({
  cookie: {
    name: "__remix_session",
    httpOnly: true,
    maxAge: 60 * 60 * 24 * 7, // 7 jours
    path: "/",
    sameSite: "lax",
    secrets: [process.env.SESSION_SECRET || "replace-this-secret-in-production"],
    secure: process.env.NODE_ENV === "production",
  },
});

// Générer un ID de session aléatoire
function generateSessionId(): string {
  return crypto.randomBytes(32).toString("hex");
}

// Récupérer la session depuis la requête
export async function getSession(request: Request) {
  const cookie = request.headers.get("Cookie");
  return sessionCookieStorage.getSession(cookie);
}

// Créer une nouvelle session utilisateur
export async function createUserSession({
  userId,
  email,
  role,
  request,
  redirectTo,
}: {
  userId: string;
  email: string;
  role: string;
  request: Request;
  redirectTo: string;
}) {
  // Générer un ID de session unique
  const sessionId = generateSessionId();
  
  // Stocker les informations de session dans Redis
  await redisSessionService.setSession(sessionId, {
    userId,
    email,
    role,
    createdAt: new Date().toISOString(),
    userAgent: request.headers.get("User-Agent"),
    ip: request.headers.get("X-Forwarded-For") || request.headers.get("CF-Connecting-IP") || "unknown",
  });
  
  // Mettre à jour la dernière connexion dans la base de données
  await prisma.user.update({
    where: { id: userId },
    data: { lastLoginAt: new Date() },
  });
  
  // Créer et retourner le cookie de session
  const cookieSession = await getSession(request);
  cookieSession.set("sessionId", sessionId);
  
  return redirect(redirectTo, {
    headers: {
      "Set-Cookie": await sessionCookieStorage.commitSession(cookieSession),
    },
  });
}

// Récupérer l'utilisateur actuel à partir de la session
export async function getCurrentUser(request: Request): Promise<UserSession | null> {
  const cookieSession = await getSession(request);
  const sessionId = cookieSession.get("sessionId");
  
  if (!sessionId) {
    return null;
  }
  
  // Récupérer les données de session depuis Redis
  const sessionData = await redisSessionService.getSession(sessionId);
  if (!sessionData) {
    return null;
  }
  
  // Prolonger automatiquement la session
  await redisSessionService.extendSession(sessionId);
  
  return {
    id: sessionData.userId,
    email: sessionData.email,
    role: sessionData.role,
  };
}

// Authentifier un utilisateur
export async function authenticateUser(email: string, password: string) {
  const user = await prisma.user.findUnique({
    where: { email, active: true },
    select: {
      id: true,
      email: true,
      password: true,
      role: true,
    },
  });
  
  if (!user) {
    return null;
  }
  
  // Vérifier le mot de passe
  const isPasswordValid = await bcrypt.compare(password, user.password);
  if (!isPasswordValid) {
    return null;
  }
  
  return {
    id: user.id,
    email: user.email,
    role: user.role,
  };
}

// Détruire la session utilisateur
export async function destroySession(request: Request) {
  const cookieSession = await getSession(request);
  const sessionId = cookieSession.get("sessionId");
  
  if (sessionId) {
    await redisSessionService.deleteSession(sessionId);
  }
  
  return redirect("/login", {
    headers: {
      "Set-Cookie": await sessionCookieStorage.destroySession(cookieSession),
    },
  });
}

// Vérifier que l'utilisateur est connecté et a un rôle spécifique
export async function requireUserRole(request: Request, roles: string[] = []) {
  const user = await getCurrentUser(request);
  
  if (!user) {
    const params = new URLSearchParams([["redirectTo", new URL(request.url).pathname]]);
    throw redirect(`/login?${params}`);
  }
  
  if (roles.length > 0 && !roles.includes(user.role)) {
    throw redirect("/unauthorized");
  }
  
  return user;
}
