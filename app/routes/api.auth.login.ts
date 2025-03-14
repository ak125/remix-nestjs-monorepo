import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";
import * as bcrypt from "bcryptjs";
import { redisSessionService } from "~/services/redis.server";
import * as crypto from "crypto";

export async function action({ request }) {
  if (request.method !== "POST") {
    return json({ error: "Method not allowed" }, { status: 405 });
  }
  
  // Extraire les données de la requête
  const body = await request.json();
  const { email, password } = body;
  
  if (!email || !password) {
    return json({ error: "Email and password are required" }, { status: 400 });
  }
  
  try {
    // Rechercher l'utilisateur par email
    const user = await prisma.user.findUnique({
      where: { email, active: true },
      select: {
        id: true,
        email: true,
        password: true,
        role: true,
      }
    });
    
    // Vérifier que l'utilisateur existe
    if (!user) {
      return json({ error: "Invalid credentials" }, { status: 401 });
    }
    
    // Vérifier le mot de passe
    const isPasswordValid = await bcrypt.compare(password, user.password);
    if (!isPasswordValid) {
      return json({ error: "Invalid credentials" }, { status: 401 });
    }
    
    // Générer un ID de session
    const sessionId = crypto.randomBytes(32).toString("hex");
    
    // Créer une session dans Redis
    await redisSessionService.setSession(sessionId, {
      userId: user.id,
      email: user.email,
      role: user.role,
      createdAt: new Date().toISOString(),
      userAgent: request.headers.get("User-Agent"),
      ip: request.headers.get("X-Forwarded-For") || request.headers.get("CF-Connecting-IP") || "unknown",
    });
    
    // Mettre à jour la dernière connexion
    await prisma.user.update({
      where: { id: user.id },
      data: { lastLoginAt: new Date() }
    });
    
    // Retourner les informations de session
    return json({
      success: true,
      sessionId,
      user: {
        id: user.id,
        email: user.email,
        role: user.role
      }
    });
    
  } catch (error) {
    console.error("Login error:", error);
    return json({ error: "An error occurred during login" }, { status: 500 });
  }
}
