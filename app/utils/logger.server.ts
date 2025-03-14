import { prisma } from "./database.server";
import fs from "fs";
import path from "path";
import { sendEmail } from "./email.server";

// Répertoire des logs
const LOG_DIRECTORY = path.join(process.cwd(), "logs");

// Création du répertoire si nécessaire
if (!fs.existsSync(LOG_DIRECTORY)) {
  fs.mkdirSync(LOG_DIRECTORY, { recursive: true });
}

// Configuration
const ERROR_LOG_PATH = path.join(LOG_DIRECTORY, "error.log");
const ACCESS_LOG_PATH = path.join(LOG_DIRECTORY, "access.log");
const ADMIN_EMAIL = process.env.ADMIN_EMAIL || "admin@automecanik.com";

// Types d'alertes
type LogLevel = "info" | "warning" | "error" | "critical";

// Interface pour les options de log
interface LogOptions {
  notifyAdmin?: boolean;
  saveToFile?: boolean;
  saveToDatabase?: boolean;
  level?: LogLevel;
  context?: string;
  metadata?: Record<string, any>;
}

// Log une erreur
export async function logError(
  error: string | Error, 
  options: LogOptions = {}
) {
  const {
    notifyAdmin = false,
    saveToFile = true,
    saveToDatabase = true,
    level = "error",
    context = "app",
    metadata = {},
  } = options;

  const timestamp = new Date();
  const errorMessage = error instanceof Error ? error.message : error;
  const errorStack = error instanceof Error ? error.stack : undefined;
  
  // Format du message de log
  const logMessage = `[${timestamp.toISOString()}] [${level.toUpperCase()}] [${context}] ${errorMessage}${
    errorStack ? `\n${errorStack}` : ""
  }\n`;

  // Enregistrement dans un fichier
  if (saveToFile) {
    fs.appendFileSync(ERROR_LOG_PATH, logMessage);
  }

  // Enregistrement en base de données (vous devrez créer ce modèle dans votre schéma Prisma)
  if (saveToDatabase) {
    try {
      await prisma.adminNotification.create({
        data: {
          type: "ERROR",
          category: context.toUpperCase(),
          title: `Erreur: ${errorMessage.substring(0, 100)}`,
          message: errorMessage,
          priority: level === "critical" ? "URGENT" : "HIGH",
          metadata: metadata,
        },
      });
    } catch (dbError) {
      console.error("Erreur lors de l'enregistrement en base de données:", dbError);
    }
  }

  // Notification par email à l'administrateur
  if (notifyAdmin) {
    try {
      await sendEmail(
        ADMIN_EMAIL,
        `⚠️ [${level.toUpperCase()}] Erreur sur Automecanik`,
        `Une erreur a été détectée sur Automecanik.\n\nContexte: ${context}\nDate: ${timestamp.toLocaleString()}\n\nDétails: ${errorMessage}${
          errorStack ? `\n\nStack trace:\n${errorStack}` : ""
        }`
      );
    } catch (emailError) {
      console.error("Erreur lors de l'envoi de l'email de notification:", emailError);
      fs.appendFileSync(
        ERROR_LOG_PATH,
        `[${new Date().toISOString()}] Échec de l'envoi de l'email de notification: ${
          emailError instanceof Error ? emailError.message : "Erreur inconnue"
        }\n`
      );
    }
  }

  // Log dans la console en développement
  if (process.env.NODE_ENV !== "production") {
    console.error(logMessage);
  }
}

// Log un événement d'accès ou d'activité
export async function logActivity(message: string, context = "app") {
  const timestamp = new Date();
  const logMessage = `[${timestamp.toISOString()}] [INFO] [${context}] ${message}\n`;
  
  fs.appendFileSync(ACCESS_LOG_PATH, logMessage);
  
  // Log dans la console en développement
  if (process.env.NODE_ENV !== "production") {
    console.log(logMessage);
  }
}
