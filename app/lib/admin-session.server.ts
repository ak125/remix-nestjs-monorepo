import { createCookieSessionStorage, redirect } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import * as crypto from "crypto";

const SESSION_SECRET = process.env.ADMIN_SESSION_SECRET || "admin-super-secret-key-change-me";
const SESSION_NAME = "admin_session";

export const adminSessionStorage = createCookieSessionStorage({
  cookie: {
    name: SESSION_NAME,
    httpOnly: true,
    maxAge: 60 * 60 * 24 * 7, // 7 days
    path: "/",
    sameSite: "lax",
    secrets: [SESSION_SECRET],
    secure: process.env.NODE_ENV === "production",
  },
});

export async function getAdminSession(request: Request) {
  const cookie = request.headers.get("Cookie");
  return adminSessionStorage.getSession(cookie);
}

export async function createAdminSession(adminId: number, redirectTo: string) {
  // Generate a unique token for this session
  const token = crypto.randomBytes(32).toString('hex');
  
  // Get expiration date (7 days from now)
  const expiresAt = new Date();
  expiresAt.setDate(expiresAt.getDate() + 7);
  
  // Create session in database
  await prisma.adminSession.create({
    data: {
      adminId,
      token,
      expiresAt,
    },
  });
  
  const session = await adminSessionStorage.getSession();
  session.set("adminToken", token);
  
  return redirect(redirectTo, {
    headers: {
      "Set-Cookie": await adminSessionStorage.commitSession(session),
    },
  });
}

export async function getAdminFromSession(request: Request) {
  const session = await getAdminSession(request);
  const token = session.get("adminToken");
  
  if (!token) {
    return null;
  }
  
  // Find the session in database
  const adminSession = await prisma.adminSession.findUnique({
    where: { token },
    include: { admin: true },
  });
  
  // Check if session exists and is not expired
  if (!adminSession || new Date() > adminSession.expiresAt) {
    // Delete expired session if exists
    if (adminSession) {
      await prisma.adminSession.delete({ where: { id: adminSession.id } });
    }
    
    return null;
  }
  
  // Return admin user
  return adminSession.admin.isActive ? adminSession.admin : null;
}

export async function requireAdmin(
  request: Request, 
  minLevel: number = 1,
  redirectTo: string = "/admin/login"
) {
  const admin = await getAdminFromSession(request);
  
  if (!admin || admin.level < minLevel) {
    const searchParams = new URLSearchParams([
      ["redirectTo", new URL(request.url).pathname]
    ]);
    
    throw redirect(`${redirectTo}?${searchParams}`);
  }
  
  return admin;
}

export async function destroyAdminSession(request: Request) {
  const session = await getAdminSession(request);
  const token = session.get("adminToken");
  
  // Delete session from database if it exists
  if (token) {
    await prisma.adminSession.deleteMany({
      where: { token },
    });
  }
  
  return redirect("/admin/login", {
    headers: {
      "Set-Cookie": await adminSessionStorage.destroySession(session),
    },
  });
}

export async function cleanupExpiredSessions() {
  try {
    const result = await prisma.adminSession.deleteMany({
      where: { expiresAt: { lt: new Date() } },
    });
    
    return { deletedCount: result.count };
  } catch (error) {
    console.error("Failed to cleanup expired sessions:", error);
    return { deletedCount: 0, error };
  }
}

export async function requirePermission(
  request: Request,
  permissionName: string,
  redirectTo: string = "/admin/access-denied"
) {
  const admin = await getAdminFromSession(request);
  
  if (!admin) {
    throw redirect("/admin/login");
  }
  
  const permission = await prisma.adminPermission.findUnique({
    where: { name: permissionName },
  });
  
  if (!permission || admin.level < permission.minLevel) {
    throw redirect(redirectTo);
  }
  
  return admin;
}
