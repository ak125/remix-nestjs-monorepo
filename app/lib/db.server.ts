import { PrismaClient } from "@prisma/client";

let prisma: PrismaClient;

declare global {
  var __db__: PrismaClient | undefined;
}

// Cette approche empêche de créer trop d'instances pendant le développement
if (process.env.NODE_ENV === "production") {
  prisma = new PrismaClient();
} else {
  if (!global.__db__) {
    global.__db__ = new PrismaClient({
      log: ["query", "info", "warn", "error"],
    });
  }
  prisma = global.__db__;
  prisma.$connect();
}

export { prisma };
