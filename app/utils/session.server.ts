import { createCookieSessionStorage, redirect } from "@remix-run/node";
import { createClient } from "redis";
import { z } from "zod";

const configSchema = z.object({
  redisUrl: z.string().url(),
  secret: z.string().min(32),
  secure: z.boolean().default(false)
});

const config = configSchema.parse({
  redisUrl: process.env.REDIS_URL || "redis://localhost:6379",
  secret: process.env.SESSION_SECRET || "change-me-in-production",
  secure: process.env.NODE_ENV === "production"
});

const redisClient = createClient({
  url: config.redisUrl
});

redisClient.connect().catch(console.error);

export const sessionStorage = createCookieSessionStorage({
  cookie: {
    name: "__session",
    httpOnly: true,
    sameSite: "lax",
    path: "/",
    secrets: [config.secret],
    secure: config.secure,
    maxAge: 60 * 60 * 24 // 24h
  }
});

export async function createUserSession(userId: string, redirectTo: string) {
  const session = await sessionStorage.getSession();
  session.set("userId", userId);

  return redirect(redirectTo, {
    headers: {
      "Set-Cookie": await sessionStorage.commitSession(session)
    }
  });
}

export async function getUserSession(request: Request) {
  return sessionStorage.getSession(request.headers.get("Cookie"));
}

export async function requireUserId(request: Request) {
  const session = await getUserSession(request);
  const userId = session.get("userId");
  
  if (!userId) {
    throw redirect("/login");
  }

  return userId;
}

export async function logout(request: Request) {
  const session = await getUserSession(request);
  
  return redirect("/", {
    headers: {
      "Set-Cookie": await sessionStorage.destroySession(session)
    }
  });
}
