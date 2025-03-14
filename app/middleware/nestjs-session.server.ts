import type { Request, Response, NextFunction } from "express";
import { redisSessionService } from "~/services/redis.server";
import * as crypto from "crypto";

/**
 * Middleware pour intégrer la gestion de session compatible avec NestJS
 * Cela permet à votre API NestJS de partager la même session que votre application Remix
 */

// Configuration du middleware
export const nestjsSessionConfig = {
  name: "nestjs.sid",
  secret: process.env.SESSION_SECRET || "replace-this-secret-in-production",
  resave: false,
  saveUninitialized: false,
  cookie: {
    httpOnly: true,
    maxAge: 1000 * 60 * 60 * 24 * 7, // 7 days
    secure: process.env.NODE_ENV === "production",
  },
};

// Middleware pour gérer la session
export const sessionMiddleware = async (req: Request, res: Response, next: NextFunction) => {
  // Extraire l'ID de session du cookie ou en créer un nouveau
  let sessionId = req.cookies[nestjsSessionConfig.name];
  
  if (!sessionId) {
    sessionId = crypto.randomBytes(32).toString("hex");
    res.cookie(nestjsSessionConfig.name, sessionId, nestjsSessionConfig.cookie);
  }
  
  // Récupérer les données de session depuis Redis
  let sessionData = await redisSessionService.getSession(sessionId) || {};
  
  // Ajouter les méthodes de gestion de session au req
  req.session = {
    ...sessionData,
    
    // Méthode pour définir une valeur de session
    set: (key: string, value: any) => {
      sessionData[key] = value;
      redisSessionService.setSession(sessionId, sessionData);
    },
    
    // Méthode pour récupérer une valeur de session
    get: (key: string) => {
      return sessionData[key];
    },
    
    // Méthode pour supprimer une valeur de session
    delete: (key: string) => {
      delete sessionData[key];
      redisSessionService.setSession(sessionId, sessionData);
    },
    
    // Méthode pour détruire la session
    destroy: (callback: (err?: Error) => void) => {
      redisSessionService.deleteSession(sessionId)
        .then(() => {
          res.clearCookie(nestjsSessionConfig.name);
          callback();
        })
        .catch((err) => callback(err));
    },
    
    // Méthode pour regénérer l'ID de session
    regenerate: (callback: (err?: Error) => void) => {
      const newSessionId = crypto.randomBytes(32).toString("hex");
      
      redisSessionService.setSession(newSessionId, sessionData)
        .then(() => redisSessionService.deleteSession(sessionId))
        .then(() => {
          sessionId = newSessionId;
          res.cookie(nestjsSessionConfig.name, newSessionId, nestjsSessionConfig.cookie);
          callback();
        })
        .catch((err) => callback(err));
    }
  };
  
  next();
};
