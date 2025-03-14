import { PrismaClient } from "@prisma/client";

// Éviter de créer plusieurs instances en développement à cause du HMR
let prisma: PrismaClient;

declare global {
  var __prisma: PrismaClient | undefined;
}

// Réutiliser l'instance existante en développement ou en créer une nouvelle
if (process.env.NODE_ENV === "production") {
  prisma = new PrismaClient();
} else {
  if (!global.__prisma) {
    global.__prisma = new PrismaClient();
  }
  prisma = global.__prisma;
}

export { prisma };
